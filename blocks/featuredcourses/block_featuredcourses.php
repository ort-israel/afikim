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
 * Featured coures block main class.
 *
 * @package    block_featuredcourses
 * @copyright  Daniel Neis <danielneis@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_featuredcourses extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_featuredcourses');
    }

    public function get_content() {
        global $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->page->requires->css('/blocks/featuredcourses/css/style.css');
        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        $this->content->text = '';

        /* Lea 2017/11 - This code was in the original plugin, but it prevented the block from displaying
         * in the dashboard because was false, and since there's no use for it later on in the code, I commented it out*/
        // The user/index.php expect course context, so get one if page has module context.
//        $currentcontext = $this->page->context->get_course_context(false);
//
//        if (empty($currentcontext)) {
//            return $this->content;
//        }
        if ($this->page->course->id == SITEID) {
            $courses = self::get_featured_courses();
            require_once($CFG->libdir . '/coursecatlib.php');
            $chelper = new coursecat_helper();

            // Lea 2017 - Add wrapping class card-group
            $this->content->text .= html_writer::start_div('card-group');

            foreach ($courses as $course) {

                $course = new course_in_list($course);

                $this->content->text .= '<div class="card coursebox">';

                $content = '';

                // Display course overview files, including COURSE IMAGE
                $contentimages = $contentfiles = '';
                $course_files = $course->get_course_overviewfiles();
                foreach ($course_files as $file) {
                    $isimage = $file->is_valid_image();
                    $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), null, $file->get_filepath(), $file->get_filename());
                    if ($isimage) {
                        $contentimages .= html_writer::tag('div',
                            html_writer::empty_tag('img', array('src' => $url, 'class' => 'courseimage')),
                            array('class' => 'courseimagewrapper'));
                    } else {
                        // if no image file, show default image
                        $url = new moodle_url('/blocks/featuredcourses/img/default.png');
                        $contentimages .= html_writer::tag('div',
                            html_writer::empty_tag('img', array('src' => $url, 'class' => 'courseimage')),
                            array('class' => 'courseimagewrapper'));
                    }
                }
                // if no image file, use default.
                if (count($course_files) == 0) {
                    $url = new moodle_url('/blocks/featuredcourses/img/default.png');
                    $contentimages .= html_writer::tag('div',
                        html_writer::empty_tag('img', array('src' => $url, 'class' => 'courseimage')),
                        array('class' => 'courseimagewrapper'));
                }


                $content .= $contentimages . $contentfiles;

                // The rest of the info bound by div
                $content .= html_writer::start_div('courseinfowrapper card-block');

                // Display CATEGORY
                /* Taken from here: https://moodle.org/mod/forum/discuss.php?d=205451#p896855 */
                global $DB;
                $category = $DB->get_record('course_categories', array('id' => $course->category));    //gets the database record from the course_categories table for the active course
                // The URL should look like this: http://schoolnet.moodle.ort.org.il/course/index.php?categoryid=2
                $content .= html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $course->category)),
                    $category->name, array('class' => 'coursecategory'));


                // Display COURSE NAME
                $coursename = $chelper->get_course_formatted_name($course);
                $content .= html_writer::tag('h3', html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                    $coursename), array('class' => 'coursename'));


                // Display metadata - AUDIENCE
                require_once($CFG->dirroot . '/ort/ort_util.php');
                $content .= \ort_util::get_metadata_course_by_field_as_list($course->id, array('audience', 'discipline'));

                // Display DESCRIPTION
                if ($course->has_summary()) {
                    $content .= html_writer::start_tag('div', array('class' => 'summary'));
                    $content .= $chelper->get_course_formatted_summary($course,
                        array('overflowdiv' => true, 'noclean' => true, 'para' => false));
                    $content .= html_writer::end_tag('div');
                }

                $content .= html_writer::end_div(); //courseinfowrapper
                // Display TAGS
                $content .= html_writer::start_div('tagswrapper card-footer');
                $tags = core_tag_tag::get_item_tags('core', 'course', $course->id);
                foreach ($tags as $tag) {
                    $content .= html_writer::link(core_tag_tag::make_url($tag->tagcollid, $tag->rawname),
                            core_tag_tag::make_display_name($tag)) . ' ';
                }
                $content .= html_writer::end_div(); // tagswrapper

                $this->content->text .= $content . '</div>';
            }
            $this->content->text .= html_writer::end_div(); // card-group
        }

        return $this->content;
    }

    public function applicable_formats() {
        //return array('all' => false, 'site' => true, 'site-index' => true);
        return array('all' => true); // Lea 2017 - latest commit, upon my request to allow block to be added everywhere
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function has_config() {
        return false; // Lea 2017 - latest commit. Fix warning that true was returned even though there is no settings page
    }

    public function cron() {
        return true;
    }

    public static function get_featured_courses() {
        global $DB;

        $sql = 'SELECT c.id, c.shortname, c.fullname, c.category, fc.sortorder
                  FROM {block_featuredcourses} fc
                  JOIN {course} c
                    ON (c.id = fc.courseid)
              ORDER BY sortorder';
        return $DB->get_records_sql($sql);
    }

    public static function delete_featuredcourse($courseid) {
        global $DB;
        return $DB->delete_records('block_featuredcourses', array('courseid' => $courseid));
    }
}
