<?php

	class MySQLParser {
		public $strings = array(
			/*'DROP TABLE',*/
			'CREATE TABLE',
			'INSERT INTO',
		);

		public $fileName = '';

		public function getSliceCount(){
			$count = 0;
			$file = fopen($this->fileName,'r');
			if($file !== false){
				while(!feof($file)) {
					$str = fgets($file);
					$replaced = 0;
					str_replace($this->strings, '', $str, $replaced);
					if($replaced){
						$count++;
					}
				}
				fclose($file);
			}
			return $count;
		}

		public function getSlice($num){
			$file = fopen($this->fileName,'r');
			$count = 0;
			$slice = '';
			if($file !== false){
				while(!feof($file)) {
					$str = fgets($file);

					$replaced = 0;
					str_replace($this->strings, '', $str, $replaced);

					if($replaced){
						if($count == $num){
							return $slice;
						}
						$slice = '';
						$count++;
					}
					$slice .= $str."\r\n";

				}
				fclose($file);
				return $slice;
			}
			return '';
		}
	}