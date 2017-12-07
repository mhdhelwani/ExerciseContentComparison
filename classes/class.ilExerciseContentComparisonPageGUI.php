<?php

include_once "./Modules/Exercise/classes/class.ilObjExercise.php";
include_once("./Modules/Exercise/classes/class.ilExAssignment.php");
require_once("./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ExerciseContentComparison/classes/Helper/class.exerciseContentComparisonHelper.php");
require_once("./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ExerciseContentComparison/classes/class.ilExerciseContentComparisonTableGUI.php");
require_once("./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ExerciseContentComparison/classes/class.ilExerciseContentComparisonErrorTableGUI.php");

/**
 * @ilCtrl_IsCalledBy ilExerciseContentComparisonPageGUI: ilUIPluginRouterGUI
 * @ilCtrl_Calls ilExerciseContentComparisonPageGUI: ilObjWorkspaceFolderGUI
 */
class ilExerciseContentComparisonPageGUI
{
    /** @var ilCtrl $ctrl */
    protected $ctrl;

    /** @var ilTemplate $tpl */
    protected $tpl;

    /** @var ilExerciseContentComparisonPlugin $plugin */
    protected $plugin;

    /** @var  ilTabsGUI $tabs */
    protected $tabs;

    /** @var  ilTabsGUI $tabs */
    protected $lng;

    /**
     * @var ilPropertyFormGUI
     */
    public $form;

    protected $user_id;

    protected $selected_assignment_id;
    /**
     * @var ilObjExercise $exercise
     */
    private $exercise;

    public function __construct()
    {
        global $ilCtrl, $tpl, $ilTabs, $ilUser, $ilias, $lng;

        // catch hack attempts
        if ($ilUser->isAnonymous()) {
            $ilias->raiseError($this->lng->txt("msg_not_available_for_anon"), $ilias->error_obj->MESSAGE);
        }

        $this->ctrl = $ilCtrl;
        $this->tpl = $tpl;
        $this->tab = $ilTabs;
        $this->plugin = new ilExerciseContentComparisonPlugin();
        $this->user_id = $ilUser->getId();
        $this->lng = $lng;
        $this->exercise = ilObjectFactory::getInstanceByRefId((int)$_GET['ref_id']);
        $this->selected_assignment_id = $_REQUEST['ass_id'];

        $this->tab->activateTab("comparison_result");
    }

    /**
     * Handles all commands, default is "comparison"
     */
    public function executeCommand()
    {
        $cmd = $this->ctrl->getCmd("showComparisonResult");

        switch ($cmd) {
            default:
                if ($this->prepareOutput()) {
                    $this->$cmd();
                }

        }
    }

    protected function showComparisonResult()
    {
        global $ilToolbar;

        include_once("./Modules/Exercise/classes/class.ilFSStorageExercise.php");
        include_once("./Modules/Exercise/classes/class.ilExAssignmentTeam.php");

        $ass = ilExAssignment::getInstancesByExercise($this->exercise->getId());

        $this->lng->loadLanguageModule("exc");

        if (!$this->selected_assignment_id && count($ass) > 0) {
            $this->selected_assignment_id = current($ass)->getId();
        }

        reset($ass);
        if (count($ass) > 1) {
            $options = array();
            /**
             * @var ilExAssignment $a
             */
            foreach ($ass as $a) {
                $options[$a->getId()] = $a->getTitle();
                if ($a->getId() == $this->selected_assignment_id) {
                    $selected_assignment = $a;
                }
            }
            include_once("./Services/Form/classes/class.ilSelectInputGUI.php");
            $si = new ilSelectInputGUI($this->lng->txt(""), "ass_id");
            $si->setOptions($options);
            $si->setValue($this->selected_assignment_id);
            $ilToolbar->addStickyItem($si);

            include_once("./Services/UIComponent/Button/classes/class.ilSubmitButton.php");
            $selectAssignmentBtn = ilSubmitButton::getInstance();
            $selectAssignmentBtn->setCaption("exc_select_ass");
            $selectAssignmentBtn->setCommand("showcomparisonResult");
            $ilToolbar->addStickyItem($selectAssignmentBtn);

            $ilToolbar->addSeparator();

            include_once("./Services/Form/classes/class.ilTextInputGUI.php");
            $thresholdInput = new ilTextInputGUI($this->plugin->txt("threshold"), "threshold");
            $thresholdInput->setValue($_POST["threshold"]);
            $ilToolbar->addInputItem($thresholdInput, true);

            $k_gramInput = new ilTextInputGUI($this->plugin->txt("k_gram"), "k_gram");
            $k_gramInput->setValue($_POST["k_gram"]);
            $ilToolbar->addInputItem($k_gramInput, true);

            $ilToolbar->addFormButton($this->plugin->txt("run_comparison"), "runComparison");
        } else if ($this->selected_assignment_id) {
            $this->ctrl->setParameter($this, "ass_id", $this->selected_assignment_id);
            $selected_assignment = current($ass);
        }

        $this->ctrl->setParameter($this, "ref_id", $_GET['ref_id']);
        $this->ctrl->setParameter($this, "ass_id", $this->selected_assignment_id);
        $ilToolbar->setFormAction($this->ctrl->getFormAction($this));

        $tbl = new ilExerciseContentComparisonTableGUI($this, "showComparisonResult", $this->exercise, $selected_assignment);

        $this->tpl->setContent($tbl->getHTML());

        $this->tab->activateSubTab("comparison_result");
        $this->tpl->show();
    }

    protected function showExerciseContentComparisonError()
    {
        global $ilToolbar;

        include_once("./Modules/Exercise/classes/class.ilFSStorageExercise.php");
        include_once("./Modules/Exercise/classes/class.ilExAssignmentTeam.php");

        $ass = ilExAssignment::getInstancesByExercise($this->exercise->getId());

        $this->lng->loadLanguageModule("exc");

        if (!$this->selected_assignment_id && count($ass) > 0) {
            $this->selected_assignment_id = current($ass)->getId();
        }

        reset($ass);
        if (count($ass) > 1) {
            $options = array();
            /**
             * @var ilExAssignment $a
             */
            foreach ($ass as $a) {
                $options[$a->getId()] = $a->getTitle();
                if ($a->getId() == $this->selected_assignment_id) {
                    $selected_assignment = $a;
                }
            }
            include_once("./Services/Form/classes/class.ilSelectInputGUI.php");
            $si = new ilSelectInputGUI($this->lng->txt(""), "ass_id");
            $si->setOptions($options);
            $si->setValue($this->selected_assignment_id);
            $ilToolbar->addStickyItem($si);

            include_once("./Services/UIComponent/Button/classes/class.ilSubmitButton.php");
            $selectAssignmentBtn = ilSubmitButton::getInstance();
            $selectAssignmentBtn->setCaption("exc_select_ass");
            $selectAssignmentBtn->setCommand("showComparisonError");
            $ilToolbar->addStickyItem($selectAssignmentBtn);
        } else if ($this->selected_assignment_id) {
            $this->ctrl->setParameter($this, "ass_id", $this->selected_assignment_id);
            $selected_assignment = current($ass);
        }

        $this->ctrl->setParameter($this, "ref_id", $_GET['ref_id']);
        $this->ctrl->setParameter($this, "ass_id", $this->selected_assignment_id);
        $ilToolbar->setFormAction($this->ctrl->getFormAction($this));

        $tbl = new ilExerciseContentComparisonErrorTableGUI($this, "showPExerciseContentComparisonErrorResult", $this->exercise, $selected_assignment);

        $this->tpl->setContent($tbl->getHTML());

        $this->tab->activateSubTab("comparison_error");
        $this->tpl->show();
    }

    /**
     * Prepare the page header, tabs etc.
     */
    protected function prepareOutput()
    {
        /** @var ilLocatorGUI $ilLocator */
        global $ilLocator;

        $ilLocator->addContextItems($_GET['ref_id']);
        $this->tpl->getStandardTemplate();
        $this->tpl->setLocator();
        $this->tpl->setTitle($this->plugin->txt("tab_text"));
        $this->tpl->setDescription($this->plugin->txt("desc_text"));
        $this->tpl->setTitleIcon(ilObject::_getIcon("", "big", "exc"));
        $this->ctrl->setParameterByClass('ilExerciseContentComparisonPageGUI', 'ref_id', $_GET['ref_id']);

        $this->tab->addSubTab(
            "comparison_result",
            $this->plugin->txt("tab_text"),
            $this->ctrl->getLinkTargetByClass(["iluipluginroutergui", "ilexercisecontentcomparisonpagegui"], "showComparisonResult"));

        $this->tab->addSubTab(
            "comparison_error",
            $this->plugin->txt("comparison_error"),
            $this->ctrl->getLinkTargetByClass(["iluipluginroutergui", "ilexercisecontentcomparisonpagegui"], "showComparisonError"));

        return true;
    }

    protected function runComparison()
    {
        $threshold = $_POST["threshold"];
        $k_gram = $_POST["k_gram"];


        if (strlen($threshold) == 0 || !is_numeric($threshold) || strlen($k_gram) == 0 || !is_numeric($k_gram)) {
            $this->lng->loadLanguageModule("form");
            ilUtil::sendFailure($this->lng->txt("form_msg_numeric_value_required"), true);
        } else {
            $this->comparison($threshold, $k_gram, $_POST["ass_id"]);
        }
        $this->showComparisonResult();
    }

    private function comparison($a_threshold, $a_k_gram, $a_ass_id)
    {
        include_once("./Modules/Exercise/classes/class.ilExAssignmentTeam.php");

        $assignment = new ilExAssignment($a_ass_id);
        $teams = ilExAssignmentTeam::getInstancesFromMap($assignment);
        $members = exerciseContentComparisonHelper::_getMembersList($assignment);
        $files = exerciseContentComparisonHelper::_getAllSubmittedFilesInAssignment($assignment, $members, $teams);

        exerciseContentComparisonHelper::_deleteExerciseContentComparison($assignment->getExerciseId(), $a_ass_id);
        exerciseContentComparisonHelper::_deleteExerciseContentComparisonError($assignment->getExerciseId(), $a_ass_id);
        $fileFingerPrintArray = [];
        $fileIndexArray = [];

        foreach ($files as $key => $file) {
            $fileInfo = new SplFileInfo($file["filename"]);
            if (!in_array($fileInfo->getExtension(), ["docx", "txt", "pdf", "doc"])) {
                exerciseContentComparisonHelper::_insertExerciseContentComparisonError(
                    $assignment->getExerciseId(),
                    $a_ass_id,
                    $file["returned_id"],
                    "unsupported_file_type"
                );
                unset($files[$key]);
            } else {
                if (!array_key_exists($file["filename"], $fileFingerPrintArray)) {
                    $fileContent = exerciseContentComparisonHelper::_getFileContent($file["filename"]);
                    list($fingerprintIndex, $fingerprintValue) = exerciseContentComparisonHelper::_getFingerprint(
                        $a_k_gram,
                        $a_threshold,
                        $fileContent
                    );

                    $fileFingerPrintArray[$file["filename"]] = $fingerprintValue;
                    $fileIndexArray[$file["filename"]] = $fingerprintIndex;

                    if ((sizeof($fileFingerPrintArray[$file["filename"]]) == 1 &&
                            $fileFingerPrintArray[$file["filename"]][0] == -1) ||
                        !$fileContent
                    ) {
                        exerciseContentComparisonHelper::_insertExerciseContentComparisonError(
                            $assignment->getExerciseId(),
                            $a_ass_id,
                            $file["returned_id"],
                            "unreadable_file"
                        );
                        unset($files[$key]);
                    }
                }
            }
        }

        foreach ($files as $file) {
            foreach ($files as $compere_with_file) {
                if ($file["user_id"] !== $compere_with_file["user_id"]) {
                    $numberOfMatched = exerciseContentComparisonHelper::_getNumberOfMatched(
                        $fileFingerPrintArray[$file["filename"]],
                        $fileIndexArray[$file["filename"]],
                        $fileFingerPrintArray[$compere_with_file["filename"]]
                    );

                    $matchPercent = ($numberOfMatched / sizeof($fileFingerPrintArray[$file["filename"]])) * 100;

                    exerciseContentComparisonHelper::_insertExerciseContentComparison(
                        $assignment->getExerciseId(),
                        $a_ass_id,
                        $file["returned_id"],
                        $compere_with_file["returned_id"],
                        $a_threshold,
                        $a_k_gram,
                        $matchPercent
                    );
                }
            }
        }
    }
}