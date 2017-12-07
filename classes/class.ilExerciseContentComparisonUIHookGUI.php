<?php
include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");

class ilExerciseContentComparisonUIHookGUI extends ilUIHookPluginGUI
{
    function modifyGUI($a_comp, $a_part, $a_par = [])
    {
        /** @var ilCtrl $ilCtrl */
        /** @var ilTabsGUI $ilTabs */
        /** @var ilAccessHandler $ilAccess */
        global $ilCtrl, $ilTabs, $ilAccess;

        if ($a_part === "tabs") {
            if ($ilCtrl->getContextObjType() === "exc" && isset($_GET['ref_id'])) {
                if (!$ilTabs->back_target && !$ilTabs->back_2_target) {
                    if ($ilAccess->checkAccess("write", "", (int)$_GET['ref_id'])) {
                        $ilCtrl->setParameterByClass('ilExerciseContentComparisonPageGUI', 'ref_id', $_GET['ref_id']);
                        $plugin = new ilExerciseContentComparisonPlugin();
                        $ilTabs->addTab(
                            "comparison",
                            $plugin->txt("tab_text"),
                            $ilCtrl->getLinkTargetByClass(["iluipluginroutergui", "ilexercisecontentcomparisonpagegui"], "showComparisonResult"));

                        $_SESSION["ExerciseContentComparison"]["TabTarget"] = $ilTabs->target;
                    }
                }
            }
            if ($ilCtrl->getCmdClass() == "ilexercisecontentcomparisonpagegui") {
                if (isset($_SESSION["ExerciseContentComparison"]["TabTarget"])) {
                    $ilTabs->target = $_SESSION["ExerciseContentComparison"]["TabTarget"];
                }
                $ilTabs->activateTab("comparison");
            }
        }
    }
}
