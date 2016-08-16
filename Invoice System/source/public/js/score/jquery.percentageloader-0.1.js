/*
jquery.percentageloader.js 
 
Copyright (c) 2012, Better2Web
All rights reserved.

This jQuery plugin is licensed under the Simplified BSD License. Please
see the file license.txt that was included with the plugin bundle.

*/

/*global jQuery */

(function ($) {
    /* Strict mode for this plugin */
    "use strict";
    /*jslint browser: true */

    /* Our spiral gradient data */
    var imgdata = "/images/score/test.jpg",
        gradient = new Image();
    gradient.src = imgdata;

    /** Percentage loader
     * @param	params	Specify options in {}. May be on of width, height, progress or value.
     *
     * @example $("#myloader-container).percentageLoader({
		    width : 256,  // width in pixels
		    height : 256, // height in pixels
		    progress: 0,  // initialise progress bar position, within the range [0..1]
		    value: '0kb'  // initialise text label to this value
		});
     */
    $.fn.percentageLoader = function (params) {
        var settings, canvas, percentageText, valueText, items, i, item, selectors, s, ctx, progress,
            value, cX, cY, lingrad, innerGrad, tubeGrad, innerRadius, innerBarRadius, outerBarRadius,
            radius, startAngle, endAngle, counterClockwise, completeAngle, setProgress, setValue,
            applyAngle, drawLoader, clipValue, outerDiv;

        /* Specify default settings */
        settings = {
            width: 256,
            height: 256,
            progress: 0,
            value: '0kb',
            controllable: false
        };

        /* Override default settings with provided params, if any */
        if (params !== undefined) {
            $.extend(settings, params);
        } else {
            params = settings;
        }

        outerDiv = document.createElement('div');
        outerDiv.style.width = settings.width + 'px';
        outerDiv.style.height = settings.height + 'px';
        outerDiv.style.position = 'relative';

        $(this).append(outerDiv);

        /* Create our canvas object */
        canvas = document.createElement('canvas');
        canvas.setAttribute('width', settings.width);
        canvas.setAttribute('height', settings.height);
        outerDiv.appendChild(canvas);

        /* Create div elements we'll use for text. Drawing text is
         * possible with canvas but it is tricky working with custom
         * fonts as it is hard to guarantee when they become available
         * with differences between browsers. DOM is a safer bet here */
        percentageText = document.createElement('div');
        percentageText.style.width = (settings.width.toString() - 2) + 'px';
        percentageText.style.textAlign = 'center';
        percentageText.style.height = '50px';
        percentageText.style.left = 0;
        percentageText.style.position = 'absolute';

        valueText = document.createElement('div');
        valueText.style.width = (settings.width - 2).toString() + 'px';
        valueText.style.textAlign = 'center';
        valueText.style.height = '0px';
        valueText.style.overflow = 'hidden';
        valueText.style.left = 0;
        valueText.style.position = 'absolute';

        /* Force text items to not allow selection */
        items = [valueText, percentageText];
        for (i  = 0; i < items.length; i += 1) {
            item = items[i];
            selectors = [
                '-webkit-user-select',
                '-khtml-user-select',
                '-moz-user-select',
                '-o-user-select',
                'user-select'];

            for (s = 0; s < selectors.length; s += 1) {
                $(item).css(selectors[s], 'none');
            }
        }

        /* Add the new dom elements to the containing div */
        outerDiv.appendChild(percentageText);
        outerDiv.appendChild(valueText);

        /* Get a reference to the context of our canvas object */
        ctx = canvas.getContext("2d");


        /* Set various initial values */

        /* Centre point */
        cX = (canvas.width / 2) - 1;
        cY = (canvas.height / 2) - 1;

        /* Create our linear gradient for the outer ring */
        lingrad = ctx.createLinearGradient(cX, 0, cX, canvas.height);
        lingrad.addColorStop(0, '#d6eeff');
        lingrad.addColorStop(1, '#b6d8f0');

        /* Create inner gradient for the outer ring */
        innerGrad = ctx.createLinearGradient(cX, cX * 0.133333, cX, canvas.height - cX * 0.133333);
        innerGrad.addColorStop(0, '#f9fcfe');
        innerGrad.addColorStop(1, '#d9ebf7');

        /* Tube gradient (background, not the spiral gradient) */
        tubeGrad = ctx.createLinearGradient(cX, 0, cX, canvas.height);
        tubeGrad.addColorStop(0, '#c1dff4');
        tubeGrad.addColorStop(1, '#aacee6');

        /* The inner circle is 2/3rds the size of the outer one */
        innerRadius = cX * 0.6666;
        /* Outer radius is the same as the width / 2, same as the centre x
        * (but we leave a little room so the borders aren't truncated) */
        radius = cX - 2;

        /* Calculate the radii of the inner tube */
        innerBarRadius = innerRadius + (cX * 0.06);
        outerBarRadius = radius - (cX * 0.06);

        /* Bottom left angle */
        startAngle = 2.1707963267949;
        /* Bottom right angle */
        endAngle = 0.9707963267949 + (Math.PI * 2.0);

        /* Nicer to pass counterClockwise / clockwise into canvas functions
        * than true / false */
        counterClockwise = false;

        /* Borders should be 1px */
        ctx.lineWidth = 1;

        /**
         * Little helper method for transforming points on a given
         * angle and distance for code clarity
         */
        applyAngle = function (point, angle, distance) {
            return {
                x : point.x + (Math.cos(angle) * distance),
                y : point.y + (Math.sin(angle) * distance)
            };
        };


        /**
         * render the widget in its entirety.
         */
        drawLoader = function () {
            /* Clear canvas entirely */
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            /*** IMAGERY ***/

            /* draw outer circle */
            ctx.fillStyle = lingrad;
            ctx.beginPath();
            ctx.strokeStyle = '#b2d5ed';
            ctx.arc(cX, cY, radius, 0, Math.PI * 2, counterClockwise);
            ctx.fill();
            ctx.stroke();

            /* draw inner circle */
            ctx.fillStyle = innerGrad;
            ctx.beginPath();
            ctx.arc(cX, cY, innerRadius, 0, Math.PI * 2, counterClockwise);
            ctx.fill();
            ctx.strokeStyle = '#b2d5edaa';
            ctx.stroke();

            ctx.beginPath();

            /**
             * Helper function - adds a path (without calls to beginPath or closePath)
             * to the context which describes the inner tube. We use this for drawing
             * the background of the inner tube (which is always at 100%) and the
             * progress meter itself, which may vary from 0-100% */
            function makeInnerTubePath(startAngle, endAngle) {
                var centrePoint, startPoint, controlAngle, capLength, c1, c2, point1, point2;
                centrePoint = {
                    x : cX,
                    y : cY
                };

                startPoint = applyAngle(centrePoint, startAngle, innerBarRadius);

                ctx.moveTo(startPoint.x, startPoint.y);

                point1 = applyAngle(centrePoint, endAngle, innerBarRadius);
                point2 = applyAngle(centrePoint, endAngle, outerBarRadius);

                controlAngle = endAngle + (3.142 / 2.0);
                /* Cap length - a fifth of the canvas size minus 4 pixels */
                capLength = (cX * 0.20) - 4;

                c1 = applyAngle(point1, controlAngle, capLength);
                c2 = applyAngle(point2, controlAngle, capLength);

                ctx.arc(cX, cY, innerBarRadius, startAngle, endAngle, false);
                ctx.bezierCurveTo(c1.x, c1.y, c2.x, c2.y, point2.x, point2.y);
                ctx.arc(cX, cY, outerBarRadius, endAngle, startAngle, true);

                point1 = applyAngle(centrePoint, startAngle, innerBarRadius);
                point2 = applyAngle(centrePoint, startAngle, outerBarRadius);

                controlAngle = startAngle - (3.142 / 2.0);

                c1 = applyAngle(point2, controlAngle, capLength);
                c2 = applyAngle(point1, controlAngle, capLength);

                ctx.bezierCurveTo(c1.x, c1.y, c2.x, c2.y, point1.x, point1.y);
            }

            /* Background tube */
            ctx.beginPath();
            ctx.strokeStyle = '#bcd4e5';
            makeInnerTubePath(startAngle, endAngle);

            ctx.fillStyle = tubeGrad;
            ctx.fill();
            ctx.stroke();

            /* Calculate angles for the the progress metre */
            completeAngle = startAngle + (progress * (endAngle - startAngle));

            ctx.beginPath();
            makeInnerTubePath(startAngle, completeAngle);

            /* We're going to apply a clip so save the current state */
            ctx.save();
            /* Clip so we can apply the image gradient */
            ctx.clip();

            /* Draw the spiral gradient over the clipped area */
            ctx.drawImage(gradient, 0, 0, canvas.width, canvas.height);

            /* Undo the clip */
            ctx.restore();

            /* Draw the outline of the path */
            ctx.beginPath();
            makeInnerTubePath(startAngle, completeAngle);
            ctx.stroke();

            /*** TEXT ***/
            (function () {
                var fontSize, string, smallSize, heightRemaining;
                /* Calculate the size of the font based on the canvas size */
                fontSize = cX / 2;

                percentageText.style.top = ((settings.height / 2) - (fontSize / 2)).toString() + 'px';
                percentageText.style.color = '#80a9c8';
                percentageText.style.font = fontSize.toString() + 'px BebasNeueRegular';
                percentageText.style.textShadow = '0 1px 1px #FFFFFF';

                /* Calculate the text for the given percentage */
                string = (progress * 100.0).toFixed(0) + '%';

                percentageText.innerHTML = string;

                /* Calculate font and placement of small 'value' text */
                smallSize = cX / 5.5;
                valueText.style.color = '#80a9c8';
                valueText.style.font = smallSize.toString() + 'px BebasNeueRegular';
                valueText.style.height = smallSize.toString() + 'px';
                valueText.style.textShadow = 'None';

                /* Ugly vertical align calculations - fit into bottom ring.
                 * The bottom ring occupes 1/6 of the diameter of the circle */
                heightRemaining = (settings.height * 0.16666666) - smallSize;
                valueText.style.top = ((settings.height * 0.8333333) + (heightRemaining / 4)).toString() + 'px';
            }());
        };

        /**
        * Check the progress value and ensure it is within the correct bounds [0..1]
        */
        clipValue = function () {
            if (progress < 0) {
                progress = 0;
            }

            if (progress > 1.0) {
                progress = 1.0;
            }
        };

        /* Sets the current progress level of the loader
         *
         * @param value the progress value, from 0 to 1. Values outside this range
         * will be clipped
         */
        setProgress = function (value) {
            /* Clip values to the range [0..1] */
            progress = value;
            clipValue();
            drawLoader();
        };

        this.setProgress = setProgress;

        setValue = function (val) {
            value = val;
            valueText.innerHTML = value;
        };

        this.setValue = setValue;
        this.setValue(settings.value);

        progress = settings.progress;
        clipValue();

        /* Do an initial draw */
        drawLoader();

        /* In controllable mode, add event handlers */
        if (params.controllable === true) {
            (function () {
                var mouseDown, getDistance, adjustProgressWithXY;
                getDistance = function (x, y) {
                    return Math.sqrt(Math.pow(x - cX, 2) + Math.pow(y - cY, 2));
                };

                mouseDown = false;

                adjustProgressWithXY = function (x, y) {
                    /* within the bar, calculate angle of touch point */
                    var pX, pY, angle, startTouchAngle, range, posValue;
                    pX = x - cX;
                    pY = y - cY;

                    angle = Math.atan2(pY, pX);
                    if (angle > Math.PI / 2.0) {
                        angle -= (Math.PI * 2.0);
                    }

                    startTouchAngle = startAngle - (Math.PI * 2.0);
                    range = endAngle - startAngle;
                    posValue = (angle - startTouchAngle) / range;
                    setProgress(posValue);

                    if (params.onProgressUpdate) {
                        /* use the progress value as this will have been clipped
                         * to the correct range [0..1] after the call to setProgress
                         */
                        params.onProgressUpdate(progress);
                    }
                };

                $(outerDiv).mousedown(function (e) {
                    var offset, x, y, distance;
                    offset = $(this).offset();
                    x = e.pageX - offset.left;
                    y = e.pageY - offset.top;

                    distance = getDistance(x, y);

                    if (distance > innerRadius && distance < radius) {
                        mouseDown = true;
                        adjustProgressWithXY(x, y);
                    }
                }).mouseup(function () {
                    mouseDown = false;
                }).mousemove(function (e) {
                    var offset, x, y;
                    if (mouseDown) {
                        offset = $(outerDiv).offset();
                        x = e.pageX - offset.left;
                        y = e.pageY - offset.top;
                        adjustProgressWithXY(x, y);
                    }
                }).mouseleave(function () {
                    mouseDown = false;
                });
            }());
        }
        return this;
    };
}(jQuery));
