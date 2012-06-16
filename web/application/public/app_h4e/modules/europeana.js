(function(Europeana) {

	Europeana.Model = Backbone.Model.extend({
		defaults: {
			api_key: 'ZICPOGYUWT',
			query: '',
			page: 1
		},
		url: function() {
			return 'http://api.europeana.eu/api/opensearch.json?searchTerms='+this.get('query')+'&startPage='+this.get('page')+'&wskey='+this.get('api_key')+'&callback=?';
		}
	});

	Europeana.Views.Main = Backbone.View.extend({});

})(readreactv.module('europeana'));