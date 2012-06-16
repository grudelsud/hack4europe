(function(Tweet) {

	var Entities = readreactv.module('media');

	Tweet.Model = Backbone.Model.extend({
		defaults: {
			media: new Entities.Collection(),
			urls: new Entities.Collection(),
			user_mentions: new Entities.Collection(),
			hashtags: new Entities.Collection()
		},
		parse: function(response) {
			var entities = response.entities;
			response.media = new Entities.Collection(entities.media);
			response.urls = new Entities.Collection(entities.urls);
			response.user_mentions = new Entities.Collection(entities.user_mentions);
			response.hashtags = new Entities.Collection(entities.hashtags);
			return response;
		}
	});

	Tweet.Collection = Backbone.Collection.extend({
		model: Tweet.Model,
		url: function() {
			return 'http://search.twitter.com/search.json?q='+this.query+'&page='+this.page+'&include_entities=true&callback=?&result_type=mixed';
		},
		parse: function(response) {
			return response.results;
		},
		page: 1,
		query: ''
	});

	Tweet.Views.Main = Backbone.View.extend({});

})(readreactv.module('tweet'));