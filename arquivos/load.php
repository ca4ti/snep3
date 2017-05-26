<?php
/**
 *  This file is part of SNEP.
 *  Para território Brasileiro leia LICENCA_BR.txt
 *  All other countries read the following disclaimer
 *
 *  SNEP is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  SNEP is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with SNEP.  If not, see <http://www.gnu.org/licenses/>.
 */

$file = $_GET['id'];

if(!isset($file)){
	echo "{'status':'error','message':'Esperando id como argumento!'}";
	exit(1);
}else{
	$exp_data = preg_match("/^[0-9]+_([0-9]+)_/",$file,$data);
	$exp_data = $data[1];
	$pattern = '/(20[12][0-9])([0-9][0-9])([0-9][0-9])/';
	$replacement = '$1-$2-$3';
	$data = preg_replace($pattern,$replacement,$exp_data);
	if(file_exists("$file.WAV")){
		header("Location: $file.WAV");
	}elseif(file_exists("$file.wav")){
		header("Location: $file.wav");
	}elseif(file_exists("storage1/$file.WAV")){
		header("Location: storage1/$file.WAV");
	}elseif(file_exists("storage1/$file.wav")){
		header("Location: storage1/$file.wav");
	}elseif(file_exists("$data/$file.WAV")){
		header("Location: $data/$file.WAV");
	}elseif(file_exists("$data/$file.wav")){
		header("Location: $data/$file.wav");
	}elseif(file_exists("storage1/$data/$file.WAV")){
		header("Location: storage1/$data/$file.WAV");
	}elseif(file_exists("storage1/$data/$file.wav")){
		header("Location: storage1/$data/$file.wav");
	}else{
		echo "{'status':'error','message':'File $file not found','date':'$data'}";
	}

}
