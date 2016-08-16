<?php

    class Export {
        /**
         * @param array $data
         * @return PHPExcel
         */
        public static function excelCreate($data, $format=''){
            $excel = new PHPExcel();
            $sheet = $excel->getActiveSheet();
            $row = 1;
            $maxColumn = 0;
            $maxRow = 1;

            foreach( $data as $result ){
                $column = 0;
                if( is_string($result)){

                }else{
                    foreach($result as $value ){
                        $column++;
                        $maxColumn = max($column, $maxColumn);
                    }
                }

                $row++;
                $maxRow = max($row, $maxRow);
            }

            $row = 1;
            foreach( $data as $result ){
                $column = 0;
                if( is_string($result) ){
                    continue;
                }else{
                    foreach($result as $value ){
                        $sheet->setCellValueByColumnAndRow($column, $row, $value);
                        $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
                        $column++;
                    }
                }

                $row++;
            }

            $row = 1;
            foreach( $data as $result ){

                for( $column = 0; $column < $maxColumn; $column++ ){
                    if( $format == 'pdf' ){
                        $styleArray = array(
                            'borders' => array(
                                'outline' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    'color' => array('argb' => 'FFFFFFFF'),
                                ),
                            ),
                        );
                        $sheet->getStyleByColumnAndRow($column, $row)->applyFromArray($styleArray);
                    }
                }

                $row++;
            }


            $row = 1;
            foreach( $data as $result ){
                if( is_string($result) ){
                    $columns = explode('|', $result);
                    foreach( $columns as $column => $attribute ){
                        $actions = str_split($attribute,2);
                        foreach( $actions as $action ){
                            switch($action){
                                case '-b':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                                    $sheet->getStyleByColumnAndRow($column, $row)->getBorders()->getBottom()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK));
                                    break;

                                case '-t':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                                    $sheet->getStyleByColumnAndRow($column, $row)->getBorders()->getTop()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK));
                                    break;

                                case 'ar':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                    break;

                                case 'ac':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    break;

                                case 'tb':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getFont()->setBold(true);
                                    break;

                                case 'ff':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getNumberFormat()->setFormatCode('0.00');
                                    break;

                                case 'mc':
                                    $sheet->mergeCellsByColumnAndRow($column, $row, $column+1, $row);
                                    break;

                                case 'h1':
                                    $sheet->getStyleByColumnAndRow($column, $row)->getFont()->setSize(16);
                                    break;
                            }
                        }
                    }
                }else{
                    $row++;
                }
            }

            $sheet->calculateColumnWidths();

            for( $column = 0; $column < $maxColumn; $column++ ){
                $width = max((120/$maxColumn), $sheet->getColumnDimensionByColumn($column)->getWidth()) * 1.15;
                $sheet->getColumnDimensionByColumn($column)->setAutoSize(false);
                $sheet->getColumnDimensionByColumn($column)->setWidth($width);
            }

            for( $row = 0; $row < $maxRow; $row++ ){
                //$sheet->getRowDimension($row)->setRowHeight($sheet->getRowDimension($row)->getRowHeight() * 1.05);
                $sheet->getRowDimension($row)->setzeroHeight(9);
            }

            return $excel;
        }

        public static function excelOutput(PHPExcel $excel, $name, $extension){
            $contentType = '';
            $filename = $name . '.' . $extension;

            if( $extension == 'pdf' ){
                $contentType = 'application/pdf';
                PHPExcel_Settings::setPdfRenderer(PHPExcel_Settings::PDF_RENDERER_MPDF, dirname(APPLICATION_PATH) . "/library/MPDF");
            }else{
                $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            }


            $writer = PHPExcel_IOFactory::createWriter($excel, $extension == 'pdf' ? 'PDF' : 'Excel2007');

            header('Content-Type: ' . $contentType);
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');

            die();
        }
    }