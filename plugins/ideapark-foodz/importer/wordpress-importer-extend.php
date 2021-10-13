<?php

class WP_Importer_Base {
	var $message = '';
	var $step_total = 0;
	var $step_done = 0;
}

class WP_Importer_Extend extends WP_Import {
	var $message = '';
	var $step_total = 1;
	var $step_done = 0;
	var $_posts = [];

	function importing() {
		add_filter( 'import_post_meta_key', [ $this, 'is_valid_meta_key' ] );
		add_filter( 'http_request_timeout', [ &$this, 'bump_request_timeout' ] );
		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );

		$processed = 0;
		wp_suspend_cache_invalidation( true );
		foreach ( $this->_posts as $post ) {
			$processed ++;
			$this->posts = [ $post ];
			$this->process_posts();
			if ( $post['post_type'] == 'attachment' ) {
				$this->step_done ++;
				if ( !$this->message ) {
					$this->message = __( 'Import media', 'ideapark-foodz' ) . ': ' . $post['post_title'];
				}
				if ($this->fetch_attachments && !$this->post_exists) {
					break;
				} elseif ($processed >= 10) {
					break;
				}
			} elseif ( $post['post_type'] == 'product' || $post['post_type'] == 'post' || $post['post_type'] == 'page' ) {
				$this->step_done ++;
				if ( !$this->message ) {
					$this->message = __( 'Import ', 'ideapark-foodz' ) . $post['post_type'] . ': ' . $post['post_title'];
				}
			}
			if ( memory_get_usage() / (1024 * 1024) > str_replace('M','', ini_get('memory_limit')) * 0.8 ) {
				break;
			}
		}
		$this->_posts = array_slice( $this->_posts, $processed );
		if ( count( $this->_posts ) ) {
			return true;
		} else {
			return false;
		}

	}

	function import_start( $file ) {
		parent::import_start( $file );

		if ( $this->fetch_attachments || true ) {
			$this->_posts = $this->posts;

			$this->posts  = [];

			foreach ( $this->_posts as $post ) {
				if ( $post['post_type'] == 'attachment' || $post['post_type'] == 'product' || $post['post_type'] == 'post' || $post['post_type'] == 'page') {
					$this->step_total ++;
				}
			}
		}

		$this->get_author_mapping();

	}

	function import_terms() {
		add_filter( 'import_post_meta_key', [ $this, 'is_valid_meta_key' ] );
		add_filter( 'http_request_timeout', [ &$this, 'bump_request_timeout' ] );
		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );

		wp_suspend_cache_invalidation( true );
		$this->process_categories();
		$this->process_tags();
		$this->process_terms();
		wp_suspend_cache_invalidation( false );
	}

	function import_end() {

		$this->backfill_parents();
		$this->backfill_attachment_urls();
		$this->remap_featured_images();

		parent::import_end();
	}

}