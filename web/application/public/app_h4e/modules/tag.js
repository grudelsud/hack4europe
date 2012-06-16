(function(Tag) {

	Tag.Model = Backbone.Model.extend({});

	Tag.Collection = Backbone.Collection.extend({
		model: Tag.Model
	});

	Tag.Views.Main = Backbone.View.extend({});

})(readreactv.module('tag'));