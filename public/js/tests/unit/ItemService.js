describe('ItemService', function()
{
	var Item,
		BASE_PATH,
		$httpBackend;

	beforeEach(function() {
		var $injector = angular.injector([ 'ngMock', 'ngResource', 'configuration', 'areslands.services' ]);
		Item = $injector.get( 'Item' );

		BASE_PATH = $injector.get('BASE_PATH');

		$httpBackend = $injector.get('$httpBackend');
		$httpBackend.when('GET', BASE_PATH + 'item/index/1').respond({ 'saludo': 'hola' });
	});

	it('deberia traer objeto', function() {
		
		var randomItem = Item.get({ itemId: 1 });
		$httpBackend.flush();

		var r = { 'saludo': 'holas' };

		expect(randomItem).toEqual(r);
	});
});