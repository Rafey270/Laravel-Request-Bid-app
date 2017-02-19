class BidsController{
    constructor($scope, $state, $compile, DTOptionsBuilder, DTColumnBuilder, API, $http) {
        'ngInject';

        this.API = API
        this.$state = $state
        this.$scope = $scope
        $http.get('/api/bid/index').success(function(data,status,headers, config){
            var list = []
            angular.forEach(data.response, function(value){
                list.push({id: value.id, bid_type_id: value.bid_type_id, bid_template_id: value.bid_template_id, assign_by : value.assign_by, assign_to:value.assign_to})
            })
            let dataSet = list;
            $scope.vm.dtOptions = DTOptionsBuilder.newOptions()
                .withOption('data', dataSet)
                .withOption('createdRow', createdRow)
                .withOption('responsive', true)
                .withBootstrap()
            $scope.vm.dtColumns = [
                DTColumnBuilder.newColumn('id').withTitle('ID'),
                DTColumnBuilder.newColumn('bid_type_id').withTitle('Bid Type'),
                DTColumnBuilder.newColumn('bid_template_id').withTitle('Bid Template'),
                DTColumnBuilder.newColumn('assign_by').withTitle('Assign By'),
                DTColumnBuilder.newColumn('assign_to').withTitle('Assign To'),
                DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
                    .renderWith(actionsHtml)
            ]
            $scope.vm.displayTable = true
        })
        let createdRow = (row) => {
            $compile(angular.element(row).contents())($scope)
        }
        let actionsHtml = (data) => {
            return `
                <a class="btn btn-xs btn-warning" ui-sref="app.bidsedit({bidId: ${data.id}})">
                    <i class="fa fa-edit"></i>
                </a>
                &nbsp
                <button class="btn btn-xs btn-danger" ng-click="vm.delete(${data.id})">
                    <i class="fa fa-trash-o"></i>
                </button>`
        }
    }

    $onInit(){
    }
}

export const BidsComponent = {
    templateUrl: './views/app/components/bids/bids.component.html',
    controller: BidsController,
    controllerAs: 'vm',
    bindings: {}
}
