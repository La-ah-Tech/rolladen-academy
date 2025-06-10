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
 * Unit tests for the ordering question type class.
 *
 * @package   qtype_ordering
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_ordering;

use qtype_ordering;
use test_question_maker;
use qtype_ordering_edit_form;
use qtype_ordering_test_helper;
use question_bank;
use question_possible_response;
use qtype_ordering_question;
use core_question_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/ordering/questiontype.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/ordering/edit_ordering_form.php');

/**
 * Unit tests for the ordering question type class.
 *
 * @copyright 20018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \qtype_ordering
 */
class questiontype_test extends \advanced_testcase {
    /** @var qtype_ordering instance of the question type class to test. */
    protected $qtype;

    protected function setUp(): void {
        $this->qtype = new qtype_ordering();
    }

    protected function tearDown(): void {
        $this->qtype = null;
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'ordering');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_question_saving() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $questiondata = test_question_maker::get_question_data('ordering');
        $formdata = test_question_maker::get_question_form_data('ordering');

        /** @var core_question_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category([]);

        $formdata->category = "{$cat->id},{$cat->contextid}";

        qtype_ordering_edit_form::mock_submit((array) $formdata);

        $form = qtype_ordering_test_helper::get_question_editing_form($cat, $questiondata);
        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestiondata = question_bank::load_question_data($returnedfromsave->id);

        foreach ($questiondata as $property => $value) {
            if (!in_array($property, array('id', 'version', 'timemodified', 'timecreated', 'options', 'stamp'))) {
                $this->assertContainsEquals($value, (array)$actualquestiondata);
                $this->assertContainsEquals($property, array_keys((array)$actualquestiondata));
            }
        }

        foreach ($questiondata->options as $optionname => $value) {
            if ($optionname != 'answers') {
                $this->assertContainsEquals($value, (array)$actualquestiondata->options);
                $this->assertContainsEquals($optionname, array_keys((array)$actualquestiondata->options));
            }
        }

        foreach ($questiondata->options->answers as $answer) {
            $actualanswer = array_shift($actualquestiondata->options->answers);
            foreach ($answer as $ansproperty => $ansvalue) {
                if ($ansproperty === 'question') {
                    $this->assertContainsEquals($returnedfromsave->id, (array)$actualanswer);
                    $this->assertContainsEquals($ansproperty, array_keys((array)$actualanswer));
                } else if ($ansproperty !== 'id') {
                    $this->assertContainsEquals($ansvalue, (array)$actualanswer);
                    $this->assertContainsEquals($ansproperty, array_keys((array)$actualanswer));
                }
            }
        }
    }

    public function test_get_possible_responses() {
        $questiondata = test_question_maker::get_question_data('ordering');
        $possibleresponses = $this->qtype->get_possible_responses($questiondata);
        $expectedresponseclasses = array(
            'Modular' => array(
                    1 => new question_possible_response('Position 1', 0.1666667),
                    2 => new question_possible_response('Position 2', 0),
                    3 => new question_possible_response('Position 3', 0),
                    4 => new question_possible_response('Position 4', 0),
                    5 => new question_possible_response('Position 5', 0),
                    6 => new question_possible_response('Position 6', 0),
            ),
            'Object' => array(
                    1 => new question_possible_response('Position 1', 0),
                    2 => new question_possible_response('Position 2', 0.1666667),
                    3 => new question_possible_response('Position 3', 0),
                    4 => new question_possible_response('Position 4', 0),
                    5 => new question_possible_response('Position 5', 0),
                    6 => new question_possible_response('Position 6', 0),
            ),
            'Oriented' => array(
                    1 => new question_possible_response('Position 1', 0),
                    2 => new question_possible_response('Position 2', 0),
                    3 => new question_possible_response('Position 3', 0.1666667),
                    4 => new question_possible_response('Position 4', 0),
                    5 => new question_possible_response('Position 5', 0),
                    6 => new question_possible_response('Position 6', 0),
            ),
            'Dynamic' => array(
                    1 => new question_possible_response('Position 1', 0),
                    2 => new question_possible_response('Position 2', 0),
                    3 => new question_possible_response('Position 3', 0),
                    4 => new question_possible_response('Position 4', 0.1666667),
                    5 => new question_possible_response('Position 5', 0),
                    6 => new question_possible_response('Position 6', 0),
            ),
            'Learning' => array(
                    1 => new question_possible_response('Position 1', 0),
                    2 => new question_possible_response('Position 2', 0),
                    3 => new question_possible_response('Position 3', 0),
                    4 => new question_possible_response('Position 4', 0),
                    5 => new question_possible_response('Position 5', 0.1666667),
                    6 => new question_possible_response('Position 6', 0),
            ),
            'Environment' => array(
                    1 => new question_possible_response('Position 1', 0),
                    2 => new question_possible_response('Position 2', 0),
                    3 => new question_possible_response('Position 3', 0),
                    4 => new question_possible_response('Position 4', 0),
                    5 => new question_possible_response('Position 5', 0),
                    6 => new question_possible_response('Position 6', 0.1666667),
            ),
        );
        $this->assertEqualsWithDelta($expectedresponseclasses, $possibleresponses, 0.0000005, '');
    }

    public function test_get_possible_responses_very_long() {
        $questiondata = test_question_maker::get_question_data('ordering');
        $onehundredchars = str_repeat('1234567890', 9) . '123456789碁';
        // Set one of the answers to over 100 chars, with a multi-byte UTF-8 character at position 100.
        $questiondata->options->answers[13]->answer = $onehundredchars . 'and some more';
        $possibleresponses = $this->qtype->get_possible_responses($questiondata);
        $this->assertArrayHasKey($onehundredchars, $possibleresponses);
    }

    public function test_get_numberingstyle() {
        $questiondata = test_question_maker::get_question_data('ordering');
        $expected = qtype_ordering_question::NUMBERING_STYLE_DEFAULT;
        $actual = $this->qtype->get_numberingstyle($questiondata);
        $this->assertEquals($expected, $actual);

        $questiondata->options->numberingstyle = 'abc';
        $expected = 'abc';
        $actual = $this->qtype->get_numberingstyle($questiondata);
        $this->assertEquals($expected, $actual);

        $questiondata->options->numberingstyle = 'ABCD';
        $expected = 'ABCD';
        $actual = $this->qtype->get_numberingstyle($questiondata);
        $this->assertEquals($expected, $actual);

        $questiondata->options->numberingstyle = '123';
        $expected = '123';
        $actual = $this->qtype->get_numberingstyle($questiondata);
        $this->assertEquals($expected, $actual);

        $questiondata->options->numberingstyle = 'iii';
        $expected = 'iii';
        $actual = $this->qtype->get_numberingstyle($questiondata);
        $this->assertEquals($expected, $actual);

        $questiondata->options->numberingstyle = 'III';
        $expected = 'III';
        $actual = $this->qtype->get_numberingstyle($questiondata);
        $this->assertEquals($expected, $actual);
    }

    public function test_xml_import(): void {
        $this->resetAfterTest();
        // Import a question from XML.
        $xml = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'testimport.moodle.xml'));
        $xmldata = xmlize($xml);
        $format = new qformat_xml();
        $imported = $format->try_importing_using_qtypes(
            $xmldata['question'], null, null, 'ordering');

        $this->assert(new question_check_specified_fields_expectation(self::expectedimport()), $imported);
    }

    public function test_xml_import_long(): void {
        $this->resetAfterTest();
        // Import a question from XML.
        $xml = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'testimportlong.moodle.xml'));
        $xmldata = xmlize($xml);
        $format = new qformat_xml();
        $imported = $format->try_importing_using_qtypes(
            $xmldata['question'], null, null, 'ordering');

        $expected = self::expectedimport();
        $expected->name = 'Moodle Moodle Moodle Moodle Moodle Moodle Moodle';

        $this->assert(new question_check_specified_fields_expectation($expected), $imported);
    }

    public function test_xml_export(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category();
        $question = $generator->create_question('ordering', 'moodle',
            ['category' => $category->id, 'idnumber' => 'myid']);

        // Export it.
        $questiondata = question_bank::load_question_data($question->id);
        // Force the question id to be 123, to ensure it comes through the export.
        $questiondata->id = 123;
        $questiondata->options->numberingstyle = null;
        // Add some feedback to ensure it comes through the export.
        foreach ($questiondata->options->answers as $answer) {
            $answer->feedback = $answer->answer . ' is correct.';
            $answer->feedbackformat = FORMAT_HTML;
            $answer->feedbackfiles = 0;
        }

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($questiondata);

        $expectedxml = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'testexport.moodle.xml'));

        $this->assert_same_xml($expectedxml, $xml);
    }

    public function test_gift_import(): void {
        $this->resetAfterTest();
        // Import a question from GIFT.
        $gift = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'testimport.gift.txt'));
        $format = new qformat_gift();
        $lines = preg_split('/[\\n\\r]/', phpunit_util::normalise_line_endings($gift));
        $imported = $format->readquestion($lines);

        $this->assert(new question_check_specified_fields_expectation(self::expectedimport()), $imported);
    }

    public function test_gift_export(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category();
        $question = $generator->create_question('ordering', 'moodle',
            ['category' => $category->id, 'idnumber' => 'myid']);

        // Export it.
        $questiondata = question_bank::load_question_data($question->id);
        // Force the question id to be 123, to ensure it comes through the export.
        $questiondata->id = 123;

        $exporter = new qformat_gift();
        $gift = $exporter->writequestion($questiondata);

        $expectedgift = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'testexport.gift.txt'));

        $this->assertEquals(
            phpunit_util::normalise_line_endings($expectedgift),
            phpunit_util::normalise_line_endings($gift)
        );
    }
}
