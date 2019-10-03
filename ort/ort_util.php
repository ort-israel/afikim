<?php
/**
 * Created by PhpStorm.
 * User: lcohen
 * Date: 16/11/2017
 * Time: 13:56
 */

class ort_util {

    /**
     * Gets all metadata associated with given course
     * @param $courseid
     * @return array
     */
    public static function get_metadata_course($courseid) {
        global $DB;
        $ret = array();
        if (\ort_util::table_exists('local_metadata_field')) {
            $allcoursefields = $DB->get_records('local_metadata_field', array('contextlevel' => CONTEXT_COURSE));
            foreach ($allcoursefields as $coursefield) {
                $fieldvalue = $DB->get_record('local_metadata', array('instanceid' => $courseid, 'fieldid' => $coursefield->id));
                if ($fieldvalue) {
                    $ret[$coursefield->shortname] = $fieldvalue;
                    //add the name of field:
                    $ret[$coursefield->shortname]->fieldname = $coursefield->name;
                }
            }
        }
        return $ret;
    }

    public static function get_metadata_course_by_field_as_list($courseid, $fieldnames) {
        if (!is_array($fieldnames)) {
            $fieldnames = array($fieldnames);
        }
        $ret = '';
        $metafields = \ort_util::get_metadata_course($courseid);
        foreach ($fieldnames as $fieldname) {
            if (!empty($metafields) && count($metafields) > 0) {
                if (isset($metafields[$fieldname]))
                    $ret .= html_writer::tag('dl',
                        html_writer::tag('dt',
                            $metafields[$fieldname]->fieldname . ':&nbsp; ',
                            array('class' => 'fielddefinition')) .
                        html_writer::tag('dd', $metafields[$fieldname]->data,
                            array('class' => 'fieldvalue')),
                        array('class' => 'metadata metadata' . $fieldname));
            }
        }
        return $ret;
    }

    public static function get_metadata_course_by_field_as_link($courseid, $fieldnames) {
        if (!is_array($fieldnames)) {
            $fieldnames = array($fieldnames);
        }
        $ret = '';
        $metafields = \ort_util::get_metadata_course($courseid);
        foreach ($fieldnames as $fieldname) {
            if (!empty($metafields) && count($metafields) > 0) {
                if (isset($metafields[$fieldname]))
                    $ret .= html_writer::link($metafields[$fieldname]->data, $metafields[$fieldname]->fieldname, array('class' => 'metadata metadata' . $fieldname));
            }
        }
        return $ret;
    }

    /**
     * @param $tablename
     * @return bool
     */
    public static function table_exists($tablename) {
        global $DB, $CFG;
        $result = $DB->record_exists_sql('select table_name from information_schema.tables where table_name="'.$DB->get_prefix().$tablename.'" and table_schema="'.$CFG->dbname.'"');

        return $result;
    }
}