<?php
/**
 * bbPress
 * @since 0.1
 * @version 1.2
 */
if ( defined( 'myCRED_VERSION' ) ) {
	/**
	 * Register Hook
	 * @since 0.1
	 * @version 1.0
	 */
	add_filter( 'mycred_setup_hooks', 'bbPress_myCRED_Hook' );
	function bbPress_myCRED_Hook( $installed ) {
		$installed['hook_bbpress'] = array(
			'title'       => __( 'bbPress' ),
			'description' => __( 'Awards %_plural% for bbPress actions.', 'mycred' ),
			'callback'    => array( 'myCRED_bbPress' )
		);
		return $installed;
	}

	/**
	 * Exclude bbPress Post Types
	 * @since 0.1
	 * @version 1.0
	 */
	add_filter( 'mycred_post_type_excludes', 'mycred_exclude_post_type_bbPress' );
	function mycred_exclude_post_type_bbPress( $excludes ) {
		$excludes[] = bbp_get_forum_post_type();
		$excludes[] = bbp_get_topic_post_type();
		$excludes[] = bbp_get_reply_post_type();
		return $excludes;
	}

	/**
	 * Insert Points Balance in Profile
	 * @since 0.1
	 * @version 1.0
	 */
	add_action( 'bbp_template_after_user_profile', 'mycred_bbp_add_balance_in_profile' );
	function mycred_bbp_add_balance_in_profile() {
		$user_id = bbp_get_displayed_user_id();
		$mycred = mycred_get_settings();

		if ( $mycred->exclude_user( $user_id ) ) return;

		$balance = $mycred->get_users_cred( $user_id );
		echo '<div class="users-mycred-balance">' . $mycred->plural() . ': ' . $mycred->format_creds( $balance ) . '</div>';
	}

	/**
	 * bbPress Hook
	 * @since 0.1
	 * @version 1.2
	 */
	if ( !class_exists( 'myCRED_bbPress' ) && class_exists( 'myCRED_Hook' ) ) {
		class myCRED_bbPress extends myCRED_Hook {
			/**
			 * Construct
			 */
			function __construct( $hook_prefs ) {
				parent::__construct( array(
					'id'       => 'hook_bbpress',
					'defaults' => array(
						'new_forum' => array(
							'creds'    => 1,
							'log'      => '%plural% for new forum'
						),
						'delete_forum' => array(
							'creds'    => 0-1,
							'log'      => '%singular% deduction for deleted forum'
						),
						'new_topic' => array(
							'creds'    => 1,
							'log'      => '%plural% for new forum topic',
							'author'   => 0
						),
						'delete_topic' => array(
							'creds'    => 0-1,
							'log'      => '%singular% deduction for deleted topic'
						),
						'fav_topic' => array(
							'creds'    => 1,
							'log'      => '%plural% for someone favorited your forum topic',
							'limit'    => 1
						),
						'new_reply' => array(
							'creds'    => 1,
							'log'      => '%plural% for new forum reply',
							'author'   => 0,
							'limit'    => 10,
						),
						'delete_reply' => array(
							'creds'    => 0-1,
							'log'      => '%singular% deduction for deleted reply'
						),
						'show_points_in_reply' => 0
					)
				), $hook_prefs );
			}

			/**
			 * Run
			 * @since 0.1
			 * @version 1.2
			 */
			public function run() {
				// Insert Points balance in profile
				if ( isset( $this->prefs['show_points_in_reply'] ) && $this->prefs['show_points_in_reply'] == 1 )
					add_action( 'bbp_theme_after_reply_author_details', array( $this, 'insert_balance' ) );

				// New Forum
				if ( $this->prefs['new_forum']['creds'] != 0 )
					add_action( 'bbp_new_forum', array( $this, 'new_forum' ), 20 );
				// Delete Forum
				if ( $this->prefs['delete_forum']['creds'] != 0 )
					add_action( 'bbp_delete_forum', array( $this, 'delete_forum' ) );
				// New Topic
				if ( $this->prefs['new_topic']['creds'] != 0 )
					add_action( 'bbp_new_topic', array( $this, 'new_topic' ), 20, 4 );
				// Delete Topic
				if ( $this->prefs['delete_topic']['creds'] != 0 )
					add_action( 'bbp_delete_topic', array( $this, 'delete_topic' ) );
				// Fave Topic
				if ( $this->prefs['fav_topic']['creds'] != 0 )
					add_action( 'bbp_add_user_favorite', array( $this, 'fav_topic' ), 10, 2 );
				// New Reply
				if ( $this->prefs['new_reply']['creds'] != 0 )
					add_action( 'bbp_new_reply', array( $this, 'new_reply' ), 20, 5 );
				// Delete Reply
				if ( $this->prefs['delete_reply']['creds'] != 0 )
					add_action( 'bbp_delete_reply', array( $this, 'delete_reply' ) );
			}

			/**
			 * New Forum
			 * @since 1.1.1
			 * @version 1.1
			 */
			public function new_forum( $forum ) {
				// Forum id
				$forum_id = $forum['forum_id'];

				// Forum author
				$forum_author = $forum['forum_author'];

				// Check if user is excluded
				if ( $this->core->exclude_user( $forum_author ) ) return;

				// Make sure this is unique event
				if ( $this->has_entry( 'new_forum', $forum_id, $forum_author ) ) return;

				// Execute
				$this->core->add_creds(
					'new_forum',
					$forum_author,
					$this->prefs['new_forum']['creds'],
					$this->prefs['new_forum']['log'],
					$forum_id,
					array( 'ref_type' => 'post' )
				);
			}

			/**
			 * Delete Forum
			 * @since 1.2
			 * @version 1.0
			 */
			public function delete_forum( $forum_id ) {
				// Get Author
				$forum_author = bbp_get_forum_author_id( $forum_id );

				// If gained, points, deduct
				if ( $this->has_entry( 'new_forum', $forum_id, $forum_author ) ) {

					// Execute
					$this->core->add_creds(
						'deleted_forum',
						$forum_author,
						$this->prefs['delete_forum']['creds'],
						$this->prefs['delete_forum']['log'],
						$forum_id,
						array( 'ref_type' => 'post' )
					);

				}
			}

			/**
			 * New Topic
			 * @since 0.1
			 * @version 1.1
			 */
			public function new_topic( $topic_id, $forum_id, $anonymous_data, $topic_author ) {
				// Check if user is excluded
				if ( $this->core->exclude_user( $topic_author ) ) return;

				// Check if forum author is allowed to get points for their own topics
				if ( (bool) $this->prefs['new_topic']['author'] == false ) {
					if ( bbp_get_forum_author_id( $forum_id ) == $topic_author ) return;
				}

				// Make sure this is unique event
				if ( $this->has_entry( 'new_forum_topic', $topic_id, $topic_author ) ) return;

				// Execute
				$this->core->add_creds(
					'new_forum_topic',
					$topic_author,
					$this->prefs['new_topic']['creds'],
					$this->prefs['new_topic']['log'],
					$topic_id,
					array( 'ref_type' => 'post' )
				);
			}

			/**
			 * Delete Topic
			 * @since 1.2
			 * @version 1.0
			 */
			public function delete_topic( $topic_id ) {
				// Get Author
				$topic_author = bbp_get_topic_author_id( $topic_id );

				// If gained, points, deduct
				if ( $this->has_entry( 'new_forum_topic', $topic_id, $topic_author ) ) {

					// Execute
					$this->core->add_creds(
						'deleted_topic',
						$topic_author,
						$this->prefs['delete_topic']['creds'],
						$this->prefs['delete_topic']['log'],
						$topic_id,
						array( 'ref_type' => 'post' )
					);

				}
			}

			/**
			 * Topic Added to Favorites
			 * @by Fee (http://wordpress.org/support/profile/wdfee)
			 * @since 1.1.1
			 * @version 1.2
			 */
			public function fav_topic( $user_id, $topic_id ) {
				// $user_id is loggedin_user, not author, so get topic author
				$topic_author = get_post_field( 'post_author', $topic_id );

				// Enforce Daily Limit
				if ( $this->reached_daily_limit( $topic_author, 'fav_topic' ) ) return;

				// Check if user is excluded (required)
				if ( $this->core->exclude_user( $topic_author ) || $topic_author == $user_id ) return;

				// Make sure this is a unique event (favorite not from same user)
				if ( $this->has_entry( 'topic_favorited', $topic_id, $topic_author, 's:8:"ref_user";i:' . $user_id . ';' ) ) return;

				// Execute
				$this->core->add_creds(
					'topic_favorited',
					$topic_author,
					$this->prefs['fav_topic']['creds'],
					$this->prefs['fav_topic']['log'],
					$topic_id,
					array( 'ref_user' => $user_id, 'ref_type' => 'post' )
				);

				// Update Limit
				$this->update_daily_limit( $topic_author, 'fav_topic' );
			}

			/**
			 * New Reply
			 * @since 0.1
			 * @version 1.2
			 */
			public function new_reply( $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author ) {
				// Check if user is excluded
				if ( $this->core->exclude_user( $reply_author ) ) return;

				// Check if topic author gets points for their own replies
				if ( (bool) $this->prefs['new_reply']['author'] === false ) {
					if ( bbp_get_topic_author_id( $topic_id ) == $reply_author ) return;
				}

				// Check daily limit
				if ( $this->reached_daily_limit( $reply_author, 'new_reply' ) ) return;

				// Make sure this is unique event
				if ( $this->has_entry( 'new_forum_reply', $reply_id, $reply_author ) ) return;

				// Execute
				$this->core->add_creds(
					'new_forum_reply',
					$reply_author,
					$this->prefs['new_reply']['creds'],
					$this->prefs['new_reply']['log'],
					$reply_id,
					array( 'ref_type' => 'post' )
				);

				// Update Limit
				$this->update_daily_limit( $topic_author, 'new_reply' );
			}

			/**
			 * Delete Reply
			 * @since 1.2
			 * @version 1.0
			 */
			public function delete_reply( $reply_id ) {
				// Get Author
				$reply_author = bbp_get_reply_author_id( $reply_id );

				// If gained, points, deduct
				if ( $this->has_entry( 'new_forum_reply', $reply_id, $reply_author ) ) {

					// Execute
					$this->core->add_creds(
						'deleted_reply',
						$reply_author,
						$this->prefs['delete_reply']['creds'],
						$this->prefs['delete_reply']['log'],
						$reply_id,
						array( 'ref_type' => 'post' )
					);

				}
			}

			/**
			 * Insert Balance
			 * @since 0.1
			 * @version 1.1
			 */
			public function insert_balance() {
				$reply_id = bbp_get_reply_id();
				if ( bbp_is_reply_anonymous( $reply_id ) ) return;

				$balance = $this->core->get_users_cred( bbp_get_reply_author_id( $reply_id ) );
				echo '<div class="mycred-balance">' . $this->core->plural() . ': ' . $this->core->format_creds( $balance ) . '</div>';
			}

			/**
			 * Reched Daily Limit
			 * Checks if a user has reached their daily limit.
			 * @since 1.2
			 * @version 1.0
			 */
			public function reached_daily_limit( $user_id, $id ) {
				// No limit used
				if ( $this->prefs[$id]['limit'] == 0 ) return false;
				$today = date( 'Y-m-d' );
				$current = get_user_meta( $user_id, 'mycred_bbp_limits_' . $id, true );
				if ( empty( $current ) || !array_key_exists( $today, (array) $current ) ) $current[$today] = 0;
				if ( $current[ $today ] < $this->prefs[$id]['limit'] ) return false;
				return true;
			}

			/**
			 * Update Daily Limit
			 * Updates a given users daily limit.
			 * @since 1.2
			 * @version 1.0
			 */
			public function update_daily_limit( $user_id, $id ) {
				// No limit used
				if ( $this->prefs[$id]['limit'] == 0 ) return;

				$today = date( 'Y-m-d' );
				$current = get_user_meta( $user_id, 'mycred_bbp_limits_' . $id, true );
				if ( empty( $current ) || !array_key_exists( $today, (array) $current ) )
					$current[$today] = 0;

				$current[ $today ] = $current[ $today ]+1;

				update_user_meta( $user_id, 'mycred_bbp_limits_' . $id, $current );
			}

			/**
			 * Preferences
			 * @since 0.1
			 * @version 1.2
			 */
			public function preferences() {
				$prefs = $this->prefs;

				// Update
				if ( !isset( $prefs['show_points_in_reply'] ) )
					$prefs['show_points_in_reply'] = 0;
				if ( !isset( $prefs['new_topic']['author'] ) )
					$prefs['new_topic']['author'] = 0;
				if ( !isset( $prefs['fav_topic'] ) )
					$prefs['fav_topic'] = array( 'creds' => 1, 'log' => '%plural% for someone favorited your forum topic' );
				if ( !isset( $prefs['new_reply']['author'] ) )
					$prefs['new_reply']['author'] = 0;
				if ( !isset( $prefs['fav_topic']['limit'] ) )
					$prefs['fav_topic']['limit'] = 0;
				if ( !isset( $prefs['new_reply']['limit'] ) )
					$prefs['new_reply']['limit'] = 0; ?>

					<!-- Creds for New Forums -->
					<label for="<?php echo $this->field_id( array( 'new_forum', 'creds' ) ); ?>" class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for New Forum', 'mycred' ) ); ?></label>
					<ol id="">
						<li>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'new_forum', 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'new_forum', 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['new_forum']['creds'] ); ?>" size="8" /></div>
						</li>
						<li class="empty">&nbsp;</li>
						<li>
							<label for="<?php echo $this->field_id( array( 'new_forum', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'new_forum', 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'new_forum', 'log' ) ); ?>" value="<?php echo $prefs['new_forum']['log']; ?>" class="long" /></div>
							<span class="description"><?php _e( 'Available template tags: General, Post', 'mycred' ); ?></span>
						</li>
					</ol>
					<!-- Creds for Deleting Forums -->
					<label for="<?php echo $this->field_id( array( 'delete_forum', 'creds' ) ); ?>" class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for Forum Deletion', 'mycred' ) ); ?></label>
					<ol id="">
						<li>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'delete_forum', 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'delete_forum', 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['delete_forum']['creds'] ); ?>" size="8" /></div>
						</li>
						<li class="empty">&nbsp;</li>
						<li>
							<label for="<?php echo $this->field_id( array( 'delete_forum', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'delete_forum', 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'delete_forum', 'log' ) ); ?>" value="<?php echo $prefs['delete_forum']['log']; ?>" class="long" /></div>
							<span class="description"><?php _e( 'Available template tags: General, Post', 'mycred' ); ?></span>
						</li>
					</ol>
					<!-- Creds for New Topic -->
					<label for="<?php echo $this->field_id( array( 'new_topic', 'creds' ) ); ?>" class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for New Topic', 'mycred' ) ); ?></label>
					<ol id="">
						<li>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'new_topic', 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'new_topic', 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['new_topic']['creds'] ); ?>" size="8" /></div>
						</li>
						<li class="empty">&nbsp;</li>
						<li>
							<label for="<?php echo $this->field_id( array( 'new_topic', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'new_topic', 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'new_topic', 'log' ) ); ?>" value="<?php echo $prefs['new_topic']['log']; ?>" class="long" /></div>
							<span class="description"><?php _e( 'Available template tags: General, Post', 'mycred' ); ?></span>
						</li>
						<li class="empty">&nbsp;</li>
						<li>
							<input type="checkbox" name="<?php echo $this->field_name( array( 'new_topic' => 'author' ) ); ?>" id="<?php echo $this->field_id( array( 'new_topic' => 'author' ) ); ?>" <?php checked( $prefs['new_topic']['author'], 1 ); ?> value="1" />
							<label for="<?php echo $this->field_id( array( 'new_topic' => 'author' ) ); ?>"><?php echo $this->core->template_tags_general( __( 'Forum authors can receive %_plural% for creating new topics.', 'mycred' ) ); ?></label>
						</li>
					</ol>
					<!-- Creds for Deleting Topic -->
					<label for="<?php echo $this->field_id( array( 'delete_topic', 'creds' ) ); ?>" class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for Topic Deletion', 'mycred' ) ); ?></label>
					<ol id="">
						<li>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'delete_topic', 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'delete_topic', 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['delete_topic']['creds'] ); ?>" size="8" /></div>
						</li>
						<li class="empty">&nbsp;</li>
						<li>
							<label for="<?php echo $this->field_id( array( 'delete_topic', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'delete_topic', 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'delete_topic', 'log' ) ); ?>" value="<?php echo $prefs['delete_topic']['log']; ?>" class="long" /></div>
							<span class="description"><?php _e( 'Available template tags: General, Post', 'mycred' ); ?></span>
						</li>
					</ol>
					<!-- Creds for Faved Topic -->
					<label for="<?php echo $this->field_id( array( 'fav_topic', 'creds' ) ); ?>" class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for Favorited Topic', 'mycred' ) ); ?></label>
					<ol id="">
						<li>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'fav_topic', 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'fav_topic', 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['fav_topic']['creds'] ); ?>" size="8" /></div>
						</li>
						<li class="empty">&nbsp;</li>
						<li>
							<label for="<?php echo $this->field_id( array( 'fav_topic', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'fav_topic', 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'fav_topic', 'log' ) ); ?>" value="<?php echo $prefs['fav_topic']['log']; ?>" class="long" /></div>
							<span class="description"><?php _e( 'Available template tags: General, Post', 'mycred' ); ?></span>
						</li>
						<li class="empty">&nbsp;</li>
						<li>
							<label for="<?php echo $this->field_id( array( 'fav_topic', 'limit' ) ); ?>"><?php _e( 'Daily Limit', 'mycred' ); ?></label>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'fav_topic', 'limit' ) ); ?>" id="<?php echo $this->field_id( array( 'fav_topic', 'limit' ) ); ?>" value="<?php echo $this->core->number( $prefs['fav_topic']['limit'] ); ?>" size="8" /></div>
							<span class="description"><?php _e( 'Use zero for unlimited', 'mycred' ); ?></span>
						</li>
					</ol>
					<!-- Creds for New Reply -->
					<label for="<?php echo $this->field_id( array( 'new_reply', 'creds' ) ); ?>" class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for New Reply', 'mycred' ) ); ?></label>
					<ol id="">
						<li>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'new_reply', 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'new_reply', 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['new_reply']['creds'] ); ?>" size="8" /></div>
						</li>
						<li class="empty">&nbsp;</li>
						<li>
							<label for="<?php echo $this->field_id( array( 'new_reply', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'new_reply', 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'new_reply', 'log' ) ); ?>" value="<?php echo $prefs['new_reply']['log']; ?>" class="long" /></div>
							<span class="description"><?php _e( 'Available template tags: General, Post', 'mycred' ); ?></span>
						</li>
						<li class="empty">&nbsp;</li>
						<li>
							<input type="checkbox" name="<?php echo $this->field_name( array( 'new_reply' => 'author' ) ); ?>" id="<?php echo $this->field_id( array( 'new_reply' => 'author' ) ); ?>" <?php checked( $prefs['new_reply']['author'], 1 ); ?> value="1" />
							<label for="<?php echo $this->field_id( array( 'new_reply' => 'author' ) ); ?>"><?php echo $this->core->template_tags_general( __( 'Topic authors can receive %_plural% for replying to their own Topic', 'mycred' ) ); ?></label>
						</li>
						<li class="empty">&nbsp;</li>
						<li>
							<label for="<?php echo $this->field_id( array( 'new_reply', 'limit' ) ); ?>"><?php _e( 'Daily Limit', 'mycred' ); ?></label>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'new_reply', 'limit' ) ); ?>" id="<?php echo $this->field_id( array( 'new_reply', 'limit' ) ); ?>" value="<?php echo $this->core->number( $prefs['new_reply']['limit'] ); ?>" size="8" /></div>
							<span class="description"><?php _e( 'Use zero for unlimited', 'mycred' ); ?></span>
						</li>
						<li class="empty">&nbsp;</li>
						<li>
							<input type="checkbox" name="<?php echo $this->field_name( 'show_points_in_reply' ); ?>" id="<?php echo $this->field_id( 'show_points_in_reply' ); ?>" <?php checked( $prefs['show_points_in_reply'], 1 ); ?> value="1" /> <label for="<?php echo $this->field_id( 'show_points_in_reply' ); ?>"><?php echo $this->core->template_tags_general( __( 'Show users %_plural% balance in replies', 'mycred' ) ); ?>.</label>
						</li>
					</ol>
					<!-- Creds for Deleting Reply -->
					<label for="<?php echo $this->field_id( array( 'delete_reply', 'creds' ) ); ?>" class="subheader"><?php echo $this->core->template_tags_general( __( '%plural% for Topic Deletion', 'mycred' ) ); ?></label>
					<ol id="">
						<li>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'delete_reply', 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'delete_reply', 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['delete_reply']['creds'] ); ?>" size="8" /></div>
						</li>
						<li class="empty">&nbsp;</li>
						<li>
							<label for="<?php echo $this->field_id( array( 'delete_reply', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
							<div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'delete_reply', 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'delete_reply', 'log' ) ); ?>" value="<?php echo $prefs['delete_reply']['log']; ?>" class="long" /></div>
							<span class="description"><?php _e( 'Available template tags: General, Post', 'mycred' ); ?></span>
						</li>
					</ol>
<?php			unset( $this );
			}

			/**
			 * Sanitise Preference
			 * @since 1.1.1
			 * @version 1.0
			 */
			function sanitise_preferences( $data ) {
				$new_data = $data;

				$new_data['new_topic']['author'] = ( isset( $data['new_topic']['author'] ) ) ? $data['new_topic']['author'] : 0;
				$new_data['new_reply']['author'] = ( isset( $data['new_reply']['author'] ) ) ? $data['new_reply']['author'] : 0;

				return $new_data;
			}
		}
	}
}
?>