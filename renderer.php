<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * videostream module renderering methods are defined here.
 *
 * @package    mod_videostream
 * @copyright  2017 Yedidia Klein <yedidia@openapp.co.il>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/videostream/locallib.php');

/**
 * videostream module renderer class
 */
class mod_videostream_renderer extends plugin_renderer_base {

    /**
     * Renders the videostream page header.
     *
     * @param videostream videostream
     * @return string
     */
    public function video_header($videostream) {
        global $CFG , $DB;

        $output = '';
        if ($DB->get_record('local_video_directory', ['id' => $videostream->get_instance()->videoid])->convert_status < 3) {
            $msg = get_string('video_not_ready', 'videostream');
            $name = format_string($videostream->get_instance()->name . ' - ' . $msg,
            true,
            $videostream->get_course());
        } else {
            $name = format_string($videostream->get_instance()->name,
            true,
            $videostream->get_course());
        }

        $title = $this->page->course->shortname . ': ' . $name;

        $coursemoduleid = $videostream->get_course_module()->id;
        $context = context_module::instance($coursemoduleid);

        // Header setup.
        $this->page->set_title($title);
        $this->page->set_heading($this->page->course->fullname);

        $output .= $this->output->header();
        $output .= $this->output->heading($name, 3);

        if (!empty($videostream->get_instance()->intro)) {
            $output .= $this->output->box_start('generalbox boxaligncenter', 'intro');
            $output .= format_module_intro('videostream',
                                           $videostream->get_instance(),
                                           $coursemoduleid);
            $output .= $this->output->box_end();
        }
        return $output;
    }

    /**
     * Render the footer
     *
     * @return string
     */
    public function video_footer() {
        return $this->output->footer();
    }

    /**
     * Render the videostream page
     *
     * @param videostream videostream
     * @return string The page output.
     */
    public function video_page($videostream) {
        $output = '';
        $output .= $this->video_header($videostream);
        $output .= $this->video($videostream);
        $output .= $this->video_footer();

        return $output;
    }




    /**
     * Utility function for creating the JS of video events.
     *
     * @param obj $videostream
     * @return string JS
     */
    private function video_events($videostream) {
        global $CFG;
        $sesskey = sesskey();
        $jsmediaevent="<script language='JavaScript'>
            var v = document.getElementsByTagName('video')[0];
    
            v.addEventListener('seeked', function() { sendEvent('seeked'); }, true);
            v.addEventListener('play', function() { sendEvent('play'); }, true);
            v.addEventListener('stop', function() { sendEvent('stop'); }, true);
            v.addEventListener('pause', function() { sendEvent('pause'); }, true);
            v.addEventListener('ended', function() { sendEvent('ended'); }, true);
            v.addEventListener('ratechange', function() { sendEvent('ratechange'); }, true);
            function sendEvent(event) {
                console.log(event);
                require(['jquery'], function($) {
                    $.post('".$CFG->wwwroot."/mod/videostream/ajax.php',{ mid: " . $videostream->get_course_module()->id . ",videoid: " . $videostream->get_instance()->videoid . ",action: event,sesskey: '" . $sesskey . "' } );
                });
            }
        </script>";
        return $jsmediaevent;
    }

    /**
     * Utility function for creating the video source elements HTML.
     *
     * @param obj $videostream
     * @return string HTML
     */
    public function get_video_source_elements_hls($videostream) {
        global $CFG, $OUTPUT;
		$width = ($videostream->get_instance()->responsive ?
                  '100%' : $videostream->get_instance()->width . 'px');
        $height = ($videostream->get_instance()->responsive ?
                   '100%' : $videostream->get_instance()->height . 'px');
        $value = ($videostream->get_instance()->disableseek ?
                true : false);
        $data = array('width' => $width,
                      'height' => $height,
                      'hlsstream' => $this->createHLS($videostream->get_instance()->videoid),
                      'value' => $value,
                      'wwwroot' => $CFG->wwwroot);
        $output = $OUTPUT->render_from_template("mod_videostream/hls", $data);
        $output .= $this->video_events($videostream);
        return $output;
    }


    /**
     * Utility function for creating the dash video source elements HTML.
     *
     * @param obj $videostream
     * @return string HTML
     */
    private function get_video_source_elements_dash($videostream) {
        global $CFG;
		$width = ($videostream->get_instance()->responsive ?
                  '100%' : $videostream->get_instance()->width . "px");
        $height = ($videostream->get_instance()->responsive ?
                   'auto' : $videostream->get_instance()->height . "px");

        $output = '<video id=videostream class="video-js vjs-default-skin" data-setup=\'{}\' 
                    style="position: relative !important; width: ' . $width . ' !important; height: '. $height .' !important;" 
                    controls> 
                    <track label="English" kind="subtitles" srclang="en" 
                    src="'.$CFG->wwwroot.'/local/video_directory/subs.php?video_id='.$videostream->get_instance()->videoid.'" default>
                    </video>
                        <script src="https://vjs.zencdn.net/6.6.3/video.js"></script>
                        <script src="dash/dash.all.min.js"></script>
                        <script src="dash/videojs-dash.min.js"></script>
                    <script>
                        var player = videojs("videostream",{      
                            playbackRates: [0.5, 1, 1.5, 2, 3]
                        });';
        $output .= 'player.src({ src: \'';
        $output .=  $this->createDASH($videostream->get_instance()->videoid);
        $output .= '\', type: \'application/dash+xml\'});
                            player.play();
                        </script>';
        $output .= $this->video_events($videostream);
        return $output;
    }
// <script src="https://vjs.zencdn.net/6.6.3/video.js"></script>
//<script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
    /**
     * Utility function for creating the symlink/php video source elements HTML. return a basic videojs player for php/symlink pseudo streaming
     *
     * @param obj $videostream
     *        string $type 
     * @return string HTML
     */
    private function get_video_source_elements_videojs($videostream,$type) {
        global $CFG, $DB;
		$width = ($videostream->get_instance()->responsive ?
                  '100%' : $videostream->get_instance()->width . "px");
        $height = ($videostream->get_instance()->responsive ?
                   'auto' : $videostream->get_instance()->height . "px");
        if ($DB->get_record('local_video_directory', ['id' => $videostream->get_instance()->videoid])->convert_status != 5) {   
            $output = '<video id=videostream class="video-js vjs-default-skin" data-setup=\'{"languages":{"en":{
                "The media could not be loaded, either because the server or network failed or because the format is not supported."
                :"aaa"}}}\'';
        } else {
            $output = '<video id=videostream class="video-js vjs-default-skin" data-setup=\'{}\'';
        }//>
        $output .= 'style="position: relative !important; width: ' . $width . ' !important; height: '. $height .' !important;" 
                    controls> 
                    <track label="English" kind="subtitles" srclang="en" 
                    src="'.$CFG->wwwroot.'/local/video_directory/subs.php?video_id='.$videostream->get_instance()->videoid.'" default>
                    </video>
                    <script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
                    <script>
                        var player = videojs("videostream",{      
                            playbackRates: [0.5, 1, 1.5, 2, 3]
                        });';
        $output .= 'player.src({ src: \'';
        if ($type == "symlink") {
            $output .=  $this->createSYMLINK($videostream->get_instance()->videoid);
        } else { //php
            $output .=  $CFG->wwwroot.'/local/video_directory/play.php?video_id='.$videostream->get_instance()->videoid;
        }
            $output .= '\', type: \'video/mp4\'});
                            player.play();
                        </script>';
        $output .= $this->video_events($videostream);

        return $output;
    }


    /**
     * Renders videostream video.
     *
     * @param videostream $videostream
     * @return string HTML
     */
    public function video(videostream $videostream) {
        $output  = '';
        $contextid = $videostream->get_context()->id;

        // Open videostream div.
        $vclass = ($videostream->get_instance()->responsive ?
                   'videostream videostream-responsive' : 'videostream');
        $output .= $this->output->container_start($vclass);

        // Open video tag.
        //$posterurl = $this->get_poster_image($contextid);
        //$output .= $this->get_video_element_html($videostream, $posterurl);
		$config = get_config('videostream');

        if (($config->streaming == "symlink") || ($config->streaming == "php")) {
        	// Elements for video sources. (here we get the symlink and php video)
        	$output .= $this->get_video_source_elements_videojs($videostream,$config->streaming);
		} elseif ($config->streaming == "hls") {
        	// Elements for video sources. (here we get the hls video)
        	$output .= $this->get_video_source_elements_hls($videostream);
        } else {
			//Dash video
			$output .= $this->get_video_source_elements_dash($videostream);
		}

        // Close video tag.
        $output .= html_writer::end_tag('video');
        $output .= $this->get_bookmark_controls($videostream->get_course_module()->id);

        // Close videostream div.
        $output .= $this->output->container_end();

        return $output;
    }

    public function is_teacher($user = '') {
        global $USER, $COURSE;
        if (is_siteadmin($USER)) {
            return true;
        }
        // Check if user is editingteacher.
        $context = context_course::instance($COURSE->id);
        $roles = get_user_roles($context, $USER->id, true);
        $keys = array_keys($roles);
        foreach ($keys as $key) {
            if ($roles[$key]->shortname == 'editingteacher') {
                return true;
            }
        }
        return false;
    }

	public function get_bookmark_controls($moduleid) {
		global $DB, $USER;
        $output = '';

        $isteacher = $this->is_teacher();
        $sql = "select * from mdl_videostreambookmarks
        where (userid =? or teacherid IS NOT NULL) and moduleid = ?";

        $bookmarks = $DB->get_records_sql($sql, ['userid' => $USER->id, 'moduleid' => $moduleid]);

		$bookmarks = array_values(array_map(function($a) {
			$a->bookmarkpositionvisible = gmdate("H:i:s", (int)$a->bookmarkposition);
			return $a;
        }, $bookmarks));
        
		$output .= $this->output->render_from_template('mod_videostream/bookmark_controls', ['bookmarks' => $bookmarks, 'moduleid' => $moduleid, 'isteacher' => $isteacher]);
		return $output;
    }

	public function createHLS($videoid) {
		global $DB;
		
		$config = get_config('videostream');
 
		
		$id = $videoid;
        $streams = $DB->get_records("local_video_directory_multi",array("video_id" => $id));
        if ($streams) {
		    foreach ($streams as $stream) {
			    $files[]=$stream->filename;
            }
            $hls_streaming = $config->hls_base_url;
        } else {
            $files[] = local_video_directory_get_filename($id);
            $hls_streaming = $config->hlsingle_base_url;
        }

		$parts=array();
		foreach ($files as $file) {
			$parts[] = preg_split("/[_.]/", $file);
		}
        
        $hls_url = $hls_streaming . $parts[0][0]; 
        if ($streams) {
            $hls_url .= "_";
        
		    foreach ($parts as $key => $value) {
			    $hls_url .= "," . $value[1];
            }
        }
		$hls_url .= "," . ".mp4".$config->nginx_multi."/master.m3u8";

		return $hls_url;			
	}

	public function createDASH($videoid) {
		global $DB;

		
		$config = get_config('videostream');
 
		$dash_streaming = $config->dash_base_url;
		
		$id = $videoid;
		$streams = $DB->get_records("local_video_directory_multi",array("video_id" => $id));
		foreach ($streams as $stream) {
			$files[]=$stream->filename;
		}

		$parts=array();
		foreach ($files as $file) {
			$parts[] = preg_split("/[_.]/", $file);
		}

		$dash_url = $dash_streaming . $parts[0][0] . "_";
		foreach ($parts as $key => $value) {
			$dash_url .= "," . $value[1];
		}
		$dash_url .= "," . ".mp4".$config->nginx_multi."/manifest.mpd";

		return $dash_url;			
	}

    public function createSYMLINK($videoid) {
		$config = get_config('local_video_directory');
		return $config->streaming . "/" . local_video_directory_get_filename($videoid) . ".mp4";			
	}

	
	public function get_rate_buttons() {
		$speeds = array(0.5,1,1.5,2,2.5,3);
		$output = "<div class='rates'>".get_string('playback_rate','videostream').": ";
		foreach ($speeds as $value) { 
			$output .= '<a class="playrate" onclick="document.getElementById(\'video\').playbackRate='.$value.'">X'.$value.'</a> ';	
		}
		$output .= "</div>";
		return $output;
    }
    


}
