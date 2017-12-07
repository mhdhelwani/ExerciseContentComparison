<?php
require_once("Services/Table/classes/class.ilTable2GUI.php");
include_once("./Modules/Exercise/classes/class.ilExAssignment.php");
include_once ("./Modules/Exercise/classes/class.ilExAssignmentTeam.php");
include_once "./Modules/Exercise/classes/class.ilObjExercise.php";

class ilExerciseContentComparisonTableGUI extends ilTable2GUI
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

        $this->setId("comparison_result");

        parent::__construct($a_parent_obj, $a_parent_cmd);

        $this->setTitle($this->pl->txt("comparison_result"));


        $this->addColumn($this->pl->txt("name_file_name"), "name_file_name");
        $this->addColumn($this->pl->txt("compared_with_name_file_name"), "compared_with_name_file_name");
        $this->addColumn($this->pl->txt("threshold"), "threshold");
        $this->addColumn($this->pl->txt("k_gram"), "k_gram");
        $this->addColumn($this->pl->txt("match_percent"), "match_percent");

        $this->setFormAction($ilCtrl->getFormAction($a_parent_obj, $a_parent_cmd));
        $this->setRowTemplate("tpl.comparison_list_row.html", $this->pl->getDirectory());

        $this->setPreventDoubleSubmission(false);
        $this->setDefaultOrderField("match_percent");
        $this->setDefaultOrderDirection("asc");

        $this->setData(exerciseContentComparisonHelper::_getExerciseContentComparisonResult($this->assignment));
    }

    /**
     * fill row
     * @param array $data
     */
    public function fillRow($data)
    {
        $name = "";
        $compared_with_name = "";

        foreach ($this->files as $file) {
            if ($file["returned_id"] == $data["returned_id"]){
                $name = $file["member_name"];
                if ($file["team"]){
                    asort($file["team"]);
                    $name = implode("<br/>", $file["team"]);
                }
                $name .= "<br>" . $file["filetitle"];
            }

            if ($file["returned_id"] == $data["compared_with_returned_id"]){
                $compared_with_name = $file["member_name"];
                if ($file["team"]){
                    asort($file["team"]);
                    $compared_with_name = implode("<br/>", $file["team"]);
                }
                $compared_with_name .= "<br>" . $file["filetitle"];
            }
        }

        $this->tpl->setVariable("TXT_NAME_FILE_NAME", $name);
        $this->tpl->setVariable("TXT_COMPARED_WITH_NAME_FILE_NAME", $compared_with_name);
        $this->tpl->setVariable("TXT_THRESHOLD", $data["threshold"]);
        $this->tpl->setVariable("TXT_K_GRAM", $data["k_gram"]);
        $this->tpl->setVariable("TXT_MATCH_PERCENT", number_format($data["match_percent"], 2));
        $this->tpl->parseCurrentBlock();
    }
}