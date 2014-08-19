<?php
/*
@name video
@author Nori
@link https://github.com/NoriRom/Yana-Video 
@licence Nori
@version 1.0.0
@description Plugin permettant de visionner en direct (video) par webcam
*/

 

function video_plugin_menu(&$menuItems){
	global $_;
	$menuItems[] = array('sort'=>3,'content'=>'<a href="index.php?module=video"><i class="icon-eye"></i> Video</a>');
}


function video_plugin_page($_){

//on récupère liste des vidéos
$directory = 'plugins/video/videos';
$scanned_directory = array_diff(scandir($directory,1), array('..', '.'));


	if(isset($_['module']) && $_['module']=='video'){
	?>
<link href="plugins/video/css/video.css" rel="stylesheet">;

<div id="body" class="container">

<div class="span9 userBloc">
                <h1>Video</h1>
                <p>Gestion des Vidéos de la Camera</p>
                <ul class="nav nav-tabs">
                    <?php 
                        if (isset($_['block']) && $_['block']=='play'?'class="active"':'')
                        {
                            ?>
                            <li class="active" >
                                <a href="index.php?module=video&block=play">
                                    <i class="icon-chevron-right"></i>
                                    Visionner
                                </a>
                            </li>
                            <?php
                        }
                        else
                            echo '<li><a href="index.php?module=video&block=play"><i class="icon-chevron-right"></i>Visionner</a></li>';
                        if (isset($_['block']) && $_['block']=='videoList'?'class="active"':'')
                        {
                            ?>
                            <li class="active" >
                                <a href="index.php?module=video&block=videoList">
                                    <i class="icon-chevron-right"></i>
                                    Liste Vidéos
                                </a>
                            </li>
                            <?php
                        }
                        else
                            echo '<li><a href="index.php?module=video&block=videoList"><i class="icon-chevron-right"></i>Liste Vidéos</a></li>';
                    ?>
                </ul>
                <?php 
                    if (isset($_['block']) && $_['block']=='play'?'class="active"':'')
                    {
                      if (isset($_['video']))//check si une vidéo a été sélectionnée à visionner
                          $video_to_play= $_['video'];
                      else
                          $video_to_play= $scanned_directory[0];
                        ?>

		 <button class="btn" onclick="window.location='action.php?action=video_record&time='+document.getElementById('nb_sec').value">Démarrer</button>
      <button class="btn" onclick="window.location='action.php?action=video_stop'">Arrêter</button>
       <p>Temps d'enregistrement (sec.): <INPUT id="nb_sec" type="text" maxlength="3" value="10" name="nb_sec"></p> 
      <br/>
                            <div class="span9 userBloc">
                               <video controls="controls" autoplay="autoplay" width="640" height="480">
                          			 <source src="plugins/video/videos/<?php echo $video_to_play; ?>" type="video/mp4" />
    		                       </video>
    		                    </div> <br/>   
                           
                           <!-- 
                            <OBJECT classid="clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921"
                               codebase="http://downloads.videolan.org/pub/videolan/vlc/latest/win32/axvlc.cab"
                               width="800" height="600" id="vlc" events="True">
                               <param name="Src" value="plugins/video/video.h264" />
                               <param name="ShowDisplay" value="True" />
                               <param name="AutoLoop" value="False" />
                               <param name="AutoPlay" value="True" />
                               <embed id="vlcEmb" type="application/x-google-vlc-plugin" version="VideoLAN.VLCPlugin.2" autoplay="yes" loop="no" width="640" height="480"
                               target="plugins/video/video.h264" ></embed>
                              </OBJECT> 
                              -->
                              <br/>
    		                       <div class="span6">
                                <p>
                        		  	Avant de pouvoir utiliser ce plugin, vous devez avoir branché et installé la caméra RPI.<br/> 
                                Puis passez cette commande sur votre rasp : <br/>
                                <code>sudo apt-get install -y gpac</code>   <br/>
                        		  	Et c'est tout ! Enjoy :)
                                <br/>
                        		  	<br/>
                        
                        		  </p>
                        		</div>


                        <?php
                    }
                    if (isset($_['block']) && $_['block']=='videoList'?'class="active"':'')
                    {
                         if (isset($_['action']) && $_['action']=='delete'?'class="active"':'') // check si c'est pour de la suppresion de vidéo
                         {
                           delete_video($_['video']);
                         }
                        ?>
                        <div class="span9 userBloc">
                            <table>
                            <tr>
                                <th>Video</th>
                                <th>Nom</th>
                                <th>Supprimer</th>
                            </tr>


                             <?php
                               foreach( $scanned_directory as $file ){
                               
                               echo "
                                <tr>
                                  <td><video controls=\"controls\" width=\"320\" height=\"240\">
                          			 <source src=\"plugins/video/videos/$file\" type=\"video/mp4\" />
    		                       </video></td>
                                  <td class='td2' onclick=\"javascript:location.href='index.php?module=video&block=play&video=$file'\"> $file</td>
                                  <td><a href=\"index.php?module=video&block=videoList&action=delete&video=$file\" onclick=\"return confirm('T'es sur de toi ?');\"><img src=\"plugins/video/img/corbeille.png\" width=\"50px\"></a> </td>
                                </tr>   ";
                            
                               } 
                               
                            ?>
                  </table>  
                            

                        </div>




            <?php
        }
       ?>
</div>
</div>
     <br/>
     <br/>
     <br/>
		     
		   
        
     
<?php
	}
}
 

function video_action_video(){
	global $_,$conf;
  $time="";
    
	switch($_['action']){
		case 'video_record': //enregistrement par page web   
		 if (isset($_['time']))//récupère le temps d'enregistrement
		  $time=$_['time'];
		 else     //10sec par défaut
		  $time="10";
			system("sudo raspivid -w 640 -h 480  -o plugins/video/video.h264 -t ".$time."000");
      //sleep(10);
      system("sudo MP4Box -fps 30 -add plugins/video/video.h264 plugins/video/videos/".date('Y-m-d_H.i.s').".mp4 > /dev/null &");// conversion 
      header('location:index.php?module=video');  
		break;
    
    case 'video_record_vocal': //enregistrement par commande vocale
    //pour réponse vocale
    	$affirmation = 'Enregistrement terminé';
			$response = array('responses'=>array(
									array('type'=>'talk','sentence'=>$affirmation)
												)
							);
			$json = json_encode($response);
			echo ($json=='[]'?'{}':$json);
      //enregistrement
      system("sudo raspivid -w 640 -h 480  -o plugins/video/video.h264 -t 10000");
      system("sudo MP4Box -fps 30 -add plugins/video/video.h264 plugins/video/videos/".date('Y-m-d_H.i.s').".mp4 > /dev/null &");
		break;
        
    case 'video_stop':
			system("sudo pkill -f raspivid");
			header('index.php?module=video&block=videoList');
		break;
	}
}

function delete_video($video){
  //unlink("plugins/video/videos/".$video);
  system("sudo rm plugins/video/videos/".$video);
  header("index.php?module=video&block=videoList");
}

function video_vocal_command(&$response,$actionUrl){
	global $conf;
$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME').' enregistre une vidéo',
		//'url'=>$actionUrl.'?action=record_video','confidence'=>'0.90'
		'url'=>$actionUrl.'?action=video_record_vocal','confidence'=>'0.90'
    ); 
}

function video_action(){
	global $_,$conf;
 
	switch($_['action']){
		case 'record_video':
			global $_;
				$affirmation = 'Enregistrement en cours';
				$response = array('responses'=>array(
										array('type'=>'talk','sentence'=>$affirmation)
													)
								);
				$json = json_encode($response);
				echo ($json=='[]'?'{}':$json);
        
        //lancement enregistrement
        header("action.php?action=video_record&time=10");
		break;
    } 
}

//Plugin::addJs('/js/main.js');
Plugin::addHook("action_post_case", "video_action_video");  
Plugin::addHook("menubar_pre_home", "video_plugin_menu");  
Plugin::addHook("home", "video_plugin_page");

Plugin::addHook("action_post_case", "video_action");    
Plugin::addHook("vocal_command", "video_vocal_command");
?>
