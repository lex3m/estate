<div>
	<?php
		if($data->panorama){
			foreach($data->panorama as $panoramas){
				if($panoramas->isFileExists()){
					$panoramas->renderPanorama();
				}
			}
		}
	?>
</div>
