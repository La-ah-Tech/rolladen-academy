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
 * Used to display Block Slider content anywhere in Moodle contents.
 *
 * @package    filter_slider
 * @copyright  2020 Kamil Łuczak <kamil@limsko.pl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class filter_slider
 */
class filter_slider extends moodle_text_filter {

    /**
     * Apply the filter to the text and display Slider instead of shortcode.
     *
     * @param string $text to be processed by the text
     * @param array $options filter options
     * @return string text after processing
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     * @see filter_manager::apply_filter_chain()
     */
    public function filter($text, array $options = array()) {
        global $CFG, $DB, $PAGE, $USER, $SITE;
        require_once($CFG->libdir . '/filelib.php');
        require_once($CFG->dirroot . '/blocks/moodleblock.class.php');
        require_once($CFG->dirroot . '/blocks/slider/block_slider.php');
        $pattern = "/\[SLIDER-(\d{1,5})\]/i";
        if (preg_match($pattern, $text, $output)) {
            $sliderid = $output[1];
            if ($blockinstance = $DB->get_record('block_instances', array('id' => $sliderid, 'blockname' => 'slider'))) {
                $block = new block_slider();
                $block->_load_instance($blockinstance, $PAGE);
                $content = $block->get_content();
                $contextblock = context_block::instance($blockinstance->id);
                $parentcontext = $contextblock->get_parent_context();
                $blockonfrontpage = ($SITE->id == $parentcontext->instanceid); // Skip enrolment and course capability check.
                if (!has_capability('moodle/block:view', $contextblock)
                        OR !$blockonfrontpage AND ($parentcontext->contextlevel == CONTEXT_COURSE AND !is_enrolled($parentcontext))
                        AND ($parentcontext->contextlevel == CONTEXT_COURSE
                                AND !has_capability('moodle/course:view', $parentcontext)
                        )
                ) {
                    // This user is not allowed to see this block.
                    if (isset($USER->editing) && $USER->editing) {
                        // Only when editing user can see the message.
                        return get_string('not_allowed', 'filter_slider', $sliderid);
                    }
                    // Specifically, I do not display any message to avoid confusion among users.
                    return '';
                }
                $text = preg_replace($pattern, '<div class="block_slider">' . $content->text . '</div>', $text);
            } else {
                $text = preg_replace($pattern, get_string('block_id_not_exists', 'filter_slider', $sliderid), $text);
            }
        }
        return $text;
    }
    
}