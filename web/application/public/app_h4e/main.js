var readreactv = {

	// Create this closure to contain the cached modules
	module: function() {

		// Internal module cache.
		var modules = {};

		// Create a new module reference scaffold or load an existing module.
		return function(name) {

			// If this module has already been created, return it.
			if (modules[name]) {
				return modules[name];
			}

			// Create a module and save it under this name
			return modules[name] = { Views: {} };
		};
	}(),

	fetchTemplate: function(path, done) {
		var JST = window.JST = window.JST || {};

		// Should be an instant synchronous way of getting the template, if it
		// exists in the JST object.
		if (JST[path]) {
			return done(JST[path]);
		}

		// Fetch it asynchronously if not available from JST
		return $.get(path, function(contents) {
			var tmpl = _.template(contents);

			// Set the global JST cache and return the template
			done(JST[path] = tmpl);
		});
	},
	routers: {}
};

$(function() {

	var feedModule = readreactv.module('feed');
	var feedItemModule = readreactv.module('feeditem');
	var reactionModule = readreactv.module('reaction');

	var mediaModule = readreactv.module('media');

	// Defining the application router, you can attach sub routers here.
	var Router = Backbone.Router.extend({
		routes: {
			'!/feeds/*params' : 'feeds',
			'!/reactions/id/:id' : 'reactions',
			'': 'index',
			// '*': 'clear'
		},
		initialize: function() {
			this.status = {};
			this.status.feedParams = '';
			this.status.reactionId = '';
			this.status.fetchFeeds = true;
			this.status.fetchFeedItems = true;
			this.status.fetchFeedReactions = false;

			this.views = {};
			this.models = {};
			this.collections = {};

			this.collections.feedCollection = new feedModule.Collection();
			this.views.feedCollectionView = new feedModule.Views.Collection({collection: this.collections.feedCollection});

			this.collections.feedItemCollection = new feedItemModule.Collection();
			this.views.feedItemCollectionView = new feedItemModule.Views.Collection({collection: this.collections.feedItemCollection});

			this.models.reactionModel = new reactionModule.Model();
			this.views.reactionView = new reactionModule.Views.Main({model: this.models.reactionModel});

			this.collections.mediaCollection = new mediaModule.Collection();
			this.views.mediaView = new mediaModule.Views.Main({collection: this.collections.mediaCollection});
		},
		setupPanels: function() {
			if(this.status.fetchFeeds) {
				this.collections.feedCollection.fetch();
				this.status.fetchFeeds = false;
			}
			if(this.status.fetchFeedItems) {
				this.collections.feedItemCollection.setFilter(this.status.feedParams);
				this.collections.feedItemCollection.fetch();
				this.status.fetchFeedItems = false;
			}
			if(this.status.fetchFeedReactions) {
				this.models.reactionModel.set({id: this.status.reactionId});
				this.models.reactionModel.fetch();
				this.status.fetchFeedReactions = false;
			} else {
				this.views.reactionView.empty();
			}
			if(this.status.fetchMedia) {
				this.collections.mediaCollection.fetch();
				this.status.fetchMedia = false;
			}

		},
		feeds: function( params ) {
			console.log('router - feeds ' + params);
			this.status.fetchFeedItems = true;
			this.status.feedParams = params;
			this.status.reactionId = '';

			this.setupPanels();
		},
		reactions: function( id ) {
			console.log('router - reactions ' + id);
			this.status.fetchFeedReactions = true;
			this.status.feedParams = '';
			this.status.reactionId = id;

			this.setupPanels();
		},
		index: function() {
			console.log('index');
			this.status.fetchFeeds = true;
			this.status.fetchFeedItems = true;
			this.status.fetchFeedReactions = false;
			this.status.fetchMedia = true;
			this.status.feedParams = '';
			this.status.reactionId = '';

			this.setupPanels();
		},
		clear: function() {
			this.navigate('/', {trigger: true, replace: true});
		}
	});

	//create router instance
	readreactv.routers.appRouter = new Router();
	//start history service
	Backbone.history.start();
});