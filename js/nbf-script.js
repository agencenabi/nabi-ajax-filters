;NabiFilters = (function($) {

	/**
	 * INITIALISE
	 * ----------
	 *
	 * @return {undefined}
	 */
	(function init() {

	})();


	var NABI_Filter = function (opts) {
		this.init(opts);
	};
	NABI_Filter.prototype = {
		selected: function () {
			var self = this,
				arr = this.loop($('.' + self.selected_filters));
			// Join the array with an & so we can break it later.
	        return arr.join('&');
		},
		loop: function (node) {
			// Return an array of selected navigation classes.
			var arr = [];
			node.each(function () {
				var id = $(this).data('tax');
				arr.push(id);
			});
			return arr;
		},
		filter: function (arr) {
			var self = this;
			// Return all the relevant posts...
			$.ajax({
	            url: NABI_CONFIG['ajaxurl'],
	            type: 'post',
	            data: {
	                'action': 		'nabifilterposts',
	                'filters': 		arr,
	                'posttypes': 	NABI_CONFIG['posttypes'],
	                'qo': 			NABI_CONFIG['qo'],
	                'paged': 		NABI_CONFIG['thisPage'],
	                '_ajax_nonce': 	NABI_CONFIG['nonce']
	            },
	            beforeSend: function () {
	            	self.loader.fadeIn();
	                self.section.animate({
	                	'opacity': .4
	                }, 'slow');
	            },
	            success: function (html) {
	                //alert('before');
	                self.section.empty();
	                self.section.append(html);
	                //alert('after');
	            },
	            complete: function () {
	            	self.section.animate({
	                	'opacity': 1
	            	}, 'slow');
	                self.loader.fadeOut();
	                self.running = false;

	                // Put additionnal functions here.

					// Scroll top top after changing post page.
	                $('html, body').animate( {
						scrollTop: $('#nbfPostsTop').offset().top-0 // You can change offset here if needed.
					}, 'slow');
	            },
	            error: function () {
		            console.log('error');
	            }
	        });
		},
		clicker: function () {
			var self = this;
			$('body').on('click', this.links, function (e) {
		        if (self.running == false) {
		        	// Set to true to stop function chaining.
		        	self.running = true;
		            // The following line reset the qo var so that in an ajax request it page's queried object is ignored.
		            NABI_CONFIG['qo'] = 'nbf_na';
		            // Cache some of the DOM elements for re-use later in the method.
		            var link = $(this),
		            	parent = link.parent('li'),
		            	relation = link.attr('rel');
		            if (parent.length > 0) {
		            	parent.toggleClass(self.selected_filters);
		                NABI_CONFIG['thisPage'] = 1;
		            }
		            if (relation === 'next') {
		            	NABI_CONFIG['thisPage']++;
		            } else if (relation === 'prev') {
		            	NABI_CONFIG['thisPage']--;
		            } else if (link.hasClass('nb-paginatelink')) {
		            	NABI_CONFIG['thisPage'] = relation;
		            }
		            self.filter(self.selected());
		        }
		        e.preventDefault();
		    });
		},
		init: function (opts) {
			// Set up the properties
			this.opts = opts;
			this.running = false;
			this.loader = $(this.opts['loader']);
			this.section = $(this.opts['section']);
			this.links = this.opts['links'];
			this.selected_filters = this.opts['selected_filters'];
			// Run the methods.
			this.clicker();
		}
	};

	function filterVars() {
		var nb_filter = new NABI_Filter({
			'loader': 			'#ajax-loader',
			'section': 			'#ajax-filtered-section',
			'links': 			'.ajax-filter-label, .paginationNav, .nb-paginatelink',
			'selected_filters': 'filter-selected'
		});
	}
	filterVars();

	// TODO: Add parameter in the shortcode to enable/disable multiple filters selections

	// Disable the previously clicked filter
	function toggleFilter() {
		$('.NabiFilterItem').click( function() {
			$(this).siblings().removeClass('filter-selected');
		});
	}
	toggleFilter();


	/*
		Return public methods
	 */
	return {
		toggleFilter : toggleFilter,
		filterVars : filterVars,
	}

})(jQuery);