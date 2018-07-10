<?php
include_once("./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php");

class ilExerciseContentComparisonPlugin extends ilUserInterfaceHookPlugin
{
	function getPluginName()
	{
		return "ExerciseContentComparison";
	}

    public function isCopyRightCronIsActive() {
        global $ilPluginAdmin;
        /**
         * @var ilPluginAdmin $ilPluginAdmin
         */
        return in_array('ExerciseContentComparisonCron', $ilPluginAdmin->getActivePluginsForSlot('Services', 'Cron', 'ecc_cron'));
    }

    /**
     * @return bool
     */
    public function beforeActivation() {
        //if CtrlMainMenu Plugin is active and no Video-Manager entry exists, create one
        if (self::isCopyRightCronIsActive()) {
            return true;
        }
        ilUtil::sendFailure($this->txt('msg_no_copyright_cron'), true);
        return false;
    }
}