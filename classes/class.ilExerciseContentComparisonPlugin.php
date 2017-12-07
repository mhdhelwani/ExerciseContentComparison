<?php
include_once("./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php");

class ilExerciseContentComparisonPlugin extends ilUserInterfaceHookPlugin
{
	function getPluginName()
	{
		return "ExerciseContentComparison";
	}
}