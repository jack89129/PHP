DROP FUNCTION IF EXISTS `natsort`;
CREATE FUNCTION `natsort`(s varchar(255), algorithm varchar(20)) RETURNS varchar(255) CHARSET utf8
    NO SQL
    DETERMINISTIC
BEGIN
  DECLARE orig   varchar(255)  default s;    
  DECLARE ret    varchar(255)  default '';   

  IF s IS NULL THEN

    
    RETURN NULL;

  ELSEIF s NOT REGEXP '[0-9]' THEN

    
    SET ret = s;

  ELSE

    
    

    
    

    SET s = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(s, '0', '#'), '1', '#'), '2', '#'), '3', '#'), '4', '#');
    SET s = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(s, '5', '#'), '6', '#'), '7', '#'), '8', '#'), '9', '#');
    SET s = REPLACE(s, '.#', '##');    
    SET s = REPLACE(s, '#,#', '###');  
    
    

    BEGIN

      DECLARE numpos int;
      DECLARE numlen int;
      DECLARE numstr varchar(255);

      lp1: LOOP

        
        SET numpos = LOCATE('#', s);

        
        IF numpos = 0 THEN
          SET ret = CONCAT(ret, s);
          LEAVE lp1;
        END IF;

        
        IF algorithm = 'firstnumber' AND ret = '' THEN
          
          
          
          BEGIN END;
        ELSE
          SET ret = CONCAT(ret, SUBSTRING(s, 1, numpos - 1));
        END IF;
        
        SET s    = SUBSTRING(s,    numpos);
        SET orig = SUBSTRING(orig, numpos);

        
        SET numlen = CHAR_LENGTH(s) - CHAR_LENGTH(TRIM(LEADING '#' FROM s));

        
        SET numstr = CAST(REPLACE(SUBSTRING(orig,1,numlen), ',', '') AS DECIMAL(13,3));
        
        SET numstr = LPAD(numstr, 15, '0');
        
        SET ret = CONCAT(ret, '[', numstr, ']');

        
        SET s    = SUBSTRING(s,    numlen+1);
        SET orig = SUBSTRING(orig, numlen+1);

      END LOOP;

    END;

  END IF;

  
  
  SET ret = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(ret, ' ', ''), ',', ''), ':', ''), '.', ''), ';', ''), '(', ''), ')', '');

  RETURN ret;
END;

DROP FUNCTION IF EXISTS `natsort_save`;
CREATE FUNCTION `natsort_save`(s VARCHAR(255), algorithm VARCHAR(20)) RETURNS varchar(255) CHARSET utf8
    DETERMINISTIC
BEGIN
  IF s IS NULL THEN
    RETURN NULL;
  ELSE
    SET @tmp_ := natsort(s, algorithm);
    INSERT INTO natsort_lookup_pending VALUES (algorithm, s, @tmp_);
    RETURN @tmp_;
  END IF;
END;

DROP PROCEDURE IF EXISTS `natsort_benchmark`;
CREATE PROCEDURE `natsort_benchmark`()
BEGIN
  DECLARE i INT DEFAULT 0;
  WHILE i < 1000 DO
    CALL natsort('some testing, 123 blah', 'natural');
    SET i = i + 1;
  END WHILE;
END;

DROP PROCEDURE IF EXISTS `natsort_finalize`;
CREATE PROCEDURE `natsort_finalize`()
BEGIN
  
  INSERT IGNORE INTO natsort_lookup SELECT * FROM natsort_lookup_pending;
  TRUNCATE TABLE natsort_lookup_pending;
END;

DROP PROCEDURE IF EXISTS `natsort_initialize`;
CREATE PROCEDURE `natsort_initialize`()
BEGIN
  
  
END;