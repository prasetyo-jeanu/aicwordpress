FormCraftApp.controller('MailsterController', function($scope, $http) {
	$scope.addMap = function(){
		if ($scope.SelectedList==''){return false;}
		$scope.$parent.Addons.Mailster = $scope.$parent.Addons.Mailster || {};
		$scope.$parent.Addons.Mailster.Map = $scope.$parent.Addons.Mailster.Map || [];
		$scope.$parent.Addons.Mailster.Map.push({
			'listID': $scope.SelectedList,
			'listName': jQuery('#mailster-map .select-list option:selected').text(),
			'columnID': $scope.SelectedColumn,
			'formField': jQuery('#mailster-map .select-field').val()
		});
	}
	$scope.removeMap = function ($index)
	{
		$scope.$parent.Addons.Mailster.Map.splice($index, 1);
	}
});