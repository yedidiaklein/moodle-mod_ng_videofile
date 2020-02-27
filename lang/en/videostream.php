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
 * English strings for videostream.
 *
 * @package    mod_videostream
 * @copyright  2017 Yedidia Klein <yedidia@openapp.co.il>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'videostream';
$string['modulenameplural'] = 'videostreams';
$string['modulename_help'] = 'Use the videostream module for adding html5 videos with flash fallback (using video.js). This module also allows for multi-language captions.';

$string['videostream:addinstance'] = 'Add a new videostream';
$string['videostream:view'] = 'View videostream';

$string['pluginadministration'] = 'videostream administration';
$string['pluginname'] = 'videostream';

$string['videostream_defaults_heading'] = 'Default values for videostream settings';
$string['videostream_defaults_text'] = 'The values you set here define the default values that are used in the videostream settings form when you create a new videostream.';
$string['width_explain'] = 'Specifies the default width of the video player.';
$string['height_explain'] = 'Specifies the default height of the video player.';
$string['responsive_explain'] = 'Specifies if responsive mode should be set as default or not.';
$string['limitdimensions_explain'] = 'Specifies if width and height should be used as maximum size during responsive mode.';
$string['inline'] = 'Display inline on main course page?';
$string['inline_help'] = 'Display inline on main course page?';

// <tovi
$string['disableseek'] = 'prevent to seek forward';
// tovi>

$string['filearea_captions'] = 'Captions';
$string['filearea_posters'] = 'Posters';
$string['filearea_videos'] = 'Videos';

$string['video_fieldset'] = 'Video';

$string['width'] = 'Width';
$string['width_help'] = 'Enter the width of the video here (e.g. 800 for a width of 800 pixels).';
$string['height'] = 'Height';
$string['height_help'] = 'Enter the height of the video here (e.g. 500 for a height of 500 pixels).';
$string['responsive'] = 'Responsive?';
$string['responsive_help'] = "Check to make the video automatically resize with the browser window size.\n\nUse the width and height fields to define the video proportions (e.g. 16/9 or 800/450).";
$string['responsive_label'] = '';
$string['limitdimensions'] = 'Limit size in responsive mode?';

$string['video'] = 'Video';
$string['videos'] = 'Videos';
$string['videos_help'] = "Add the video file here.\n\nYou can add alternative formats in order to be sure it can play regardless of which browser is being used (usually .mp4, .ogv and .webm covers it.)";
$string['posters'] = 'Poster Image';
$string['posters_help'] = 'Add an image here that will be displayed before the video begins playing.';
$string['captions'] = 'Captions';
$string['captions_help'] = "Add transcriptions of the dialogue in WebVTT format here.\n\nYou can add several files in order to provide multilingual captions. The file names, without extensions, will be used for the video caption option titles. If the files are named according to ISO 6392 (e.g. eng.vtt and swe.vtt) the options will be shown as the corresponding full language names according to the user's language preferences (e.g. English and Swedish, assuming the user's preferred language is set to English).";

$string['err_positive'] = 'You must enter a positive number here.';

$string['video_not_playing'] = 'Video not playing? Try {$a}.';

$string['eventvideo_view'] = 'Video Viewed';
$string['eventvideo_seek'] = 'Video Seeked';
$string['playback_rate'] = "Playback Rate";

$string['streaming_protocol'] = "Streaming Protocol";

$string['dash_base_url'] = "Dash base streaming URL";
$string['dash_base_url_explain'] = "Enter here dash base streaming URL, something like http://streaming.company.com/dash/";

$string['hls_base_url'] = "HLS base streaming URL";
$string['hls_base_url_explain'] = "Enter here HLS base streaming URL, something like http://streaming.company.com/hls/";

$string['nginx_multi'] = "NGINX multi URL string";
$string['nginx_multi_explain'] = "NGINX multi URL string, known as vod_multi_uri_suffix in nginx conf";

$string['addsmartbookmark'] = 'Add smart bookmark';
$string['time'] = 'Time';
$string['bookmarkname'] = 'Bookmark';
$string['deletebookmark'] = 'Delete this bookmark?';
