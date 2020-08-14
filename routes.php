<?php  
/*
NB: This is not stand alone code and is intended to be used within "seriti/slim3-skeleton" framework
The code snippet below is for use within an existing src/routes.php file within this framework
copy the "/asset" group into the existing "/admin" group within existing "src/routes.php" file 
*/

$app->group('/admin', function () {

    $this->group('/risk', function () {
        $this->post('/ajax', \App\Risk\Ajax::class);
        $this->any('/action', \App\Risk\ActionController::class);
        $this->any('/action_owner', \App\Risk\ActionOwnerController::class);
        $this->any('/control', \App\Risk\ControlController::class);
        $this->any('/control_owner', \App\Risk\ControlOwnerController::class);
        $this->any('/dashboard', \App\Risk\DashboardController::class);
        $this->any('/indicator', \App\Risk\IndicatorController::class);
        $this->any('/indicator_owner', \App\Risk\IndicatorOwnerController::class);
        $this->any('/objective', \App\Risk\ObjectiveController::class);
        $this->any('/objective_control', \App\Risk\ObjectiveControlController::class);
        $this->any('/objective_owner', \App\Risk\ObjectiveOwnerController::class);
        $this->any('/objective_action', \App\Risk\ObjectiveActionController::class);
        $this->any('/owner', \App\Risk\OwnerController::class);
        $this->any('/report', \App\Risk\ReportController::class);
        $this->any('/risk', \App\Risk\RiskController::class);
        $this->any('/risk_owner', \App\Risk\RiskOwnerController::class);
        $this->any('/risk_action', \App\Risk\RiskActionController::class);
        $this->any('/risk_control', \App\Risk\RiskControlController::class);
        $this->get('/setup_data', \App\Risk\SetupDataController::class);
        $this->any('/task', \App\Risk\TaskController::class);
        $this->any('/unit', \App\Risk\UnitController::class);
    })->add(\App\Risk\Config::class);

})->add(\App\User\ConfigAdmin::class);



