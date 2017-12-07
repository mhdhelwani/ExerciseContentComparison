<?php
require_once("Services/Table/classes/class.ilTable2GUI.php");
include_once("./Modules/Exercise/classes/class.ilExAssignment.php");
include_once ("./Modules/Exercise/classes/class.ilExAssignmentTeam.php");
include_once "./Modules/Exercise/classes/class.ilObjExercise.php";

class ilExerciseContentComparisonErrorTableGUI extends ilTable2GUI
{
    protected $pl;
    protected $ctrl;
    protected $tpl;
    protected $parent_obj;
    protected $assignment;
    protected $teams;
    protected $members;
    protected $files;

    /**
     * Constructor
     * @param   ilExerciseContentComparisonPageGUI $a_parent_obj
     * @param   string $a_parent_cmd
     * @param   ilObjExercise $a_exercise
     * @param   ilExAssignment $a_assignment
     */
    public function __construct($a_parent_obj, $a_parent_cmd, $a_exercise, $a_assignment)
    {
        /**
         * @var $tpl       ilTemplate
         * @var $ilCtrl    ilCtrl
         */
        global $ilCtrl, $tpl, $lng;

        $this->pl = new ilExerciseContentComparisonPlugin();
        $this->ctrl = $ilCtrl;
        $this->tpl = $tpl;
        $this->parent_obj = $a_parent_obj;
        $this->assignment = $a_assignment;
        $this->teams = ilExAssignmentTeam::getInstancesFromMap($a_assignment);
        $this->members = exerciseContentComparisonHelper::_getMembersList($a_assignment);
        $this->files = exerciseContentComparisonHelper::_getAllSubmittedFilesInAssignment($a_assignment, $this->members, $this->teams);

        $this->setId("comparison_error");

        parent::__construct($a_parent_obj, $a_parent_cmd);

        $this->setTitle($this->pl->txt("comparison_error"));


        $this->addColumn($this->pl->txt("name_file_name"), "name_file_name");
        $this->addColumn($this->pl->txt("error_text"), "error_text");

        $this->setFormAction($ilCtrl->getFormAction($a_parent_obj, $a_parent_cmd));
        $this->setRowTemplate("tpl.comparison_error_list_row.html", $this->pl->getDirectory());

        $this->setPreventDoubleSubmission(false);
        $this->setDefaultOrderField("error_text");
        $this->setDefaultOrderDirection("asc");

        $this->setData(exerciseContentComparisonHelper::_getExerciseContentComparisonError($this->assignment));
    }

    /**
     * fill row
     * @param array $data
     */
    public function fillRow($data)
    {
        $name = "";

        foreach ($this->files as $file) {
            if ($file["returned_id"] == $data["returned_id"]){
                $name = $file["member_name"];
                if ($file["team"]){
                    asort($file["team"]);
                    $name = implode("<br/>", $file["team"]);
                }
                $name .= "<br>" . $file["filetitle"];
            }
        }

        $this->tpl->setVariable("TXT_NAME_FILE_NAME", $name);
        $this->tpl->setVariable("TXT_ERROR_TEXT", $this->pl->txt($data["error_text"]));
        $this->tpl->parseCurrentBlock();
    }
}