<?php
/**
 * Class to define all the global variables related to plugin.
 *
 * @since      1.0.0
 * @author     Wbcom Designs
 * @package    Ld_Dashboard
 * @subpackage Ld_Dashboard/includes
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Ld_Dashboard_Functions' ) ) {
	/**
	 * Class to add global variables of this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	class Ld_Dashboard_Functions {
		/**
		 * The single instance of the class.
		 *
		 * @var   Ld_Dashboard_Functions
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Main Ld_Dashboard_Functions Instance.
		 *
		 * Ensures only one instance of Ld_Dashboard_Functions is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
		}

		/**
		 * Get default general settings.
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Wbcom Designs
		 * @return array
		 */
		public function default_general_settings() {
			$default_arr = array(
				'instructor-total-sales'         => 1,
			    'instructor-total-sales-bgcolor' => '#f0f0f0',
			    'course-count'                   => 1,
			    'course-count-bgcolor'           => '#f0f0f0',
			    'quizzes-count'                  => 1,
			    'quizzes-count-bgcolor'          => '#f0f0f0',
			    'assignments-count'              => 1,
			    'assignments-completed-count'    => 1,
			    'assignments-count-bgcolor'      => '#f0f0f0',
			    'essays-pending-count'           => 1,
			    'essays-pending-count-bgcolor'   => '#f0f0f0',
			    'lessons-count'                  => 1,
			    'lessons-count-bgcolor'          => '#f0f0f0',
			    'topics-count'                   => 1,
			    'topics-count-bgcolor'           => '#f0f0f0',
			    'student-count'                  => 1,
			    'student-count-bgcolor'          => '#f0f0f0',
			    'instructor-statistics'          => 1,
			    'course-progress'                => 1,
			    'enable-global-commission'       => 1,
			    'global-commission'              => 20,
			    'ins-earning'					 => 0,
			    'ins-earning-bgcolor'			 => '#b383e2',
			    'enable-email-integration'		 => 1,
				'enable-group-integration'		 => 1,
			    'enable-messaging-integration'	 => 1
			);

			return apply_filters( 'ld_dashboard_default_general_settings', $default_arr );
		}

		/**
		 * Get default activities settings.
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Wbcom Designs
		 * @return array
		 */
		public function default_activities_settings() {
			$default_arr = array(
				'enable-activity' => 1,
			    'activity-limit'  => 10,
			);

			return apply_filters( 'ld_dashboard_default_activities_settings', $default_arr );
		}

		/**
		 * Get all admin settings data.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 * @return   array
		 */
		public function ld_dashboard_settings_data() {
			$general                     = array();
			$activities                  = array();
			$default_general_settings    = $this->default_general_settings();
			$default_activities_settings = $this->default_activities_settings();
			if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
				$general_settings    = get_site_option( 'ld_dashboard_general_settings' );
			} else {
				$general_settings    = get_option( 'ld_dashboard_general_settings' );
			}
			
			if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
				$ld_dashboard_integration    = get_site_option( 'ld_dashboard_integration' );
			} else {
				$ld_dashboard_integration    = get_option( 'ld_dashboard_integration' );
			}
			
			if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
				$ld_dashboard_page_mapping    = get_site_option( 'ld_dashboard_page_mapping' );
			} else {
				$ld_dashboard_page_mapping    = get_option( 'ld_dashboard_page_mapping' );
			}

			/* General settings */
			if ( ! empty( $general_settings ) ) {
			 	if ( ! empty( $general_settings['instructor-total-sales'] ) ) {
			 		$general['instructor-total-sales'] = $general_settings['instructor-total-sales'];
			 	} else {
			 		$general['instructor-total-sales'] = 0;
			 	}
			 	if ( isset( $general_settings['instructor-total-sales-bgcolor'] ) ) {
			 		$general['instructor-total-sales-bgcolor'] = $general_settings['instructor-total-sales-bgcolor'];
			 	} else {
			 		$general['instructor-total-sales-bgcolor'] = $default_general_settings['instructor-total-sales-bgcolor'];
			 	}
			 	if ( isset( $general_settings['course-count'] ) ) {
			 		$general['course-count'] = $general_settings['course-count'];
			 	} else {
			 		$general['course-count'] = 0;
			 	}
			 	if ( isset( $general_settings['course-count-bgcolor'] ) ) {
			 		$general['course-count-bgcolor'] = $general_settings['course-count-bgcolor'];
			 	} else {
			 		$general['course-count-bgcolor'] = $default_general_settings['course-count-bgcolor'];
			 	}
			 	if ( isset( $general_settings['quizzes-count'] ) ) {
			 		$general['quizzes-count'] = $general_settings['quizzes-count'];
			 	} else {
			 		$general['quizzes-count'] = 0;
			 	}
			 	if ( isset( $general_settings['quizzes-count-bgcolor'] ) ) {
			 		$general['quizzes-count-bgcolor'] = $general_settings['quizzes-count-bgcolor'];
			 	} else {
			 		$general['quizzes-count-bgcolor'] = $default_general_settings['quizzes-count-bgcolor'];
			 	}
			 	if ( isset( $general_settings['assignments-count'] ) ) {
			 		$general['assignments-count'] = $general_settings['assignments-count'];
			 	} else {
			 		$general['assignments-count'] = 0;
			 	}
			 	if ( isset( $general_settings['assignments-completed-count'] ) ) {
			 		$general['assignments-completed-count'] = $general_settings['assignments-completed-count'];
			 	} else {
			 		$general['assignments-completed-count'] = 0;
			 	}
			 	if ( isset( $general_settings['assignments-count-bgcolor'] ) ) {
			 		$general['assignments-count-bgcolor'] = $general_settings['assignments-count-bgcolor'];
			 	} else {
			 		$general['assignments-count-bgcolor'] = $default_general_settings['assignments-count-bgcolor'];
			 	}
			 	if ( isset( $general_settings['essays-pending-count'] ) ) {
			 		$general['essays-pending-count'] = $general_settings['essays-pending-count'];
			 	} else {
			 		$general['essays-pending-count'] = 0;
			 	}
			 	if ( isset( $general_settings['essays-pending-count-bgcolor'] ) ) {
			 		$general['essays-pending-count-bgcolor'] = $general_settings['essays-pending-count-bgcolor'];
			 	} else {
			 		$general['essays-pending-count-bgcolor'] = $default_general_settings['essays-pending-count-bgcolor'];
			 	}
			 	if ( isset( $general_settings['lessons-count'] ) ) {
			 		$general['lessons-count'] = $general_settings['lessons-count'];
			 	} else {
			 		$general['lessons-count'] = 0;
			 	}
			 	if ( isset( $general_settings['lessons-count-bgcolor'] ) ) {
			 		$general['lessons-count-bgcolor'] = $general_settings['lessons-count-bgcolor'];
			 	} else {
			 		$general['lessons-count-bgcolor'] = $default_general_settings['lessons-count-bgcolor'];
			 	}
			 	if ( isset( $general_settings['topics-count'] ) ) {
			 		$general['topics-count'] = $general_settings['topics-count'];
			 	} else {
			 		$general['topics-count'] = 0;
			 	}
			 	if ( isset( $general_settings['topics-count-bgcolor'] ) ) {
			 		$general['topics-count-bgcolor'] = $general_settings['topics-count-bgcolor'];
			 	} else {
			 		$general['topics-count-bgcolor'] = $default_general_settings['topics-count-bgcolor'];
			 	}
			 	if ( isset( $general_settings['student-count'] ) ) {
			 		$general['student-count'] = $general_settings['student-count'];
			 	} else {
			 		$general['student-count'] = 0;
			 	}
			 	if ( isset( $general_settings['student-count-bgcolor'] ) ) {
			 		$general['student-count-bgcolor'] = $general_settings['student-count-bgcolor'];
			 	} else {
			 		$general['student-count-bgcolor'] = $default_general_settings['student-count-bgcolor'];
			 	}
			 	if ( isset( $general_settings['ins-earning'] ) ) {
			 		$general['ins-earning'] = $general_settings['ins-earning'];
			 	} else {
			 		$general['ins-earning'] = 0;
			 	}
			 	if ( isset( $general_settings['ins-earning-bgcolor'] ) ) {
			 		$general['ins-earning-bgcolor'] = $general_settings['ins-earning-bgcolor'];
			 	} else {
			 		$general['ins-earning-bgcolor'] = $default_general_settings['ins-earning-bgcolor'];
			 	}
			 	if ( isset( $general_settings['instructor-statistics'] ) ) {
			 		$general['instructor-statistics'] = $general_settings['instructor-statistics'];
			 	} else {
			 		$general['instructor-statistics'] = 0;
			 	}
			 	if ( isset( $general_settings['course-progress'] ) ) {
			 		$general['course-progress'] = $general_settings['course-progress'];
			 	} else {
			 		$general['course-progress'] = 0;
			 	}
			 	if ( isset( $general_settings['enable-global-commission'] ) ) {
			 		$general['enable-global-commission'] = $general_settings['enable-global-commission'];
			 	} else {
			 		$general['enable-global-commission'] = 0;
			 	}
			 	if ( isset( $general_settings['global-commission'] ) ) {
			 		$general['global-commission'] = $general_settings['global-commission'];
			 	} else {
			 		$general['global-commission'] = $default_general_settings['global-commission'];
			 	}
				
				if ( isset( $general_settings['instructor_registration_page'] ) ) {
			 		$general['instructor_registration_page'] = $general_settings['instructor_registration_page'];
			 	} else {
			 		$general['instructor_registration_page'] = 0;
			 	}

			} else {
				$general = $default_general_settings;
			}
			
			
			/*  LD Page Integration */
			if ( isset( $ld_dashboard_integration['enable-email-integration'] ) ) {
				$ld_dashboard_integration['enable-email-integration'] = $ld_dashboard_integration['enable-email-integration'];
			} else {
				$ld_dashboard_integration['enable-email-integration'] = 0;
			}
			if ( isset( $ld_dashboard_integration['enable-group-integration'] ) ) {
				$ld_dashboard_integration['enable-group-integration'] = $ld_dashboard_integration['enable-group-integration'];
			} else {
				$ld_dashboard_integration['enable-group-integration'] = 0;
			}
			if ( isset( $ld_dashboard_integration['enable-messaging-integration'] ) ) {
				$ld_dashboard_integration['enable-messaging-integration'] = $ld_dashboard_integration['enable-messaging-integration'];
			} else {
				$ld_dashboard_integration['enable-messaging-integration'] = 0;
			}
			if ( isset( $ld_dashboard_integration['display-to-do'] ) ) {
				$ld_dashboard_integration['display-to-do'] = $ld_dashboard_integration['display-to-do'];
			} else {
				$ld_dashboard_integration['display-to-do'] = 0;
			}
			
			/*  LD Page Mapping */
			if ( isset( $ld_dashboard_page_mapping['my_dashboard_page'] ) ) {
				$ld_dashboard_page_mapping['my_dashboard_page'] = $ld_dashboard_page_mapping['my_dashboard_page'];
			} else {
				$ld_dashboard_page_mapping['my_dashboard_page'] = 0;
			}
			if ( isset( $ld_dashboard_page_mapping['instructor_registration_page'] ) ) {
				$ld_dashboard_page_mapping['instructor_registration_page'] = $ld_dashboard_page_mapping['instructor_registration_page'];
			} else {
				$ld_dashboard_page_mapping['instructor_registration_page'] = 0;
			}

			/* Images settings */
			if ( isset( $activities_settings ) ) {
				if ( ! empty( $activities_settings['enable-activity'] ) ) {
					$activities['enable-activity'] = $activities_settings['enable-activity'];
				} else {
					$activities['enable-activity'] = 0;
				}
				if ( ! empty( $activities_settings['activity-limit'] ) ) {
					$activities['activity-limit'] = $activities_settings['activity-limit'];
				} else {
					$activities['activity-limit'] = $default_activities_settings['activity-limit'];
				}
			} else {
				$activities = $default_activities_settings;
			}

			$settings = array(
				'general_settings'    => $general,
				'ld_dashboard_integration'	=> $ld_dashboard_integration,
				'ld_dashboard_page_mapping'	=> $ld_dashboard_page_mapping,
				'activities_settings' => $activities,
			);

			return $settings;
		}

	}
}
