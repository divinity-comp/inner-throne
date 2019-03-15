<?php



if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly

}



/**

 * rpg_Bbp_Private_Groups_Adds class.

 *  This code was written by wolly66 anbd is included in this plugin to allow it's use

 * this class is written to add to the Private groups plugin

 * https://wordpress.org/plugins/bbp-private-groups/

 * a method to add a user to  group via code, plugins or themes

 * a method to remove a user from a group, via code, plugins or themes

 *

 * when you need to add or remove a user to a group

 * e.g. $bbp_private_groups_adds = new rpg_Bbp_Private_Groups_Adds( $user_id , $group );

 * Please, if you do not pass $user_id AND $group, the class do nothing

 *

 * Then, if you want to add a user to a group call the add() method

 * $bbp_private_groups_adds->add();

 *

 * if you want to remove a user from a group, call the remove method

 * $bbp_private_groups_adds->remove();

 *

 */

class rpg_Bbp_Private_Groups_Adds{



	private $user_id = '';

	private $group = '';

	private $group_prefix = '';

	private $user_meta_name = '';

	private $errors = array();





	/**

	 * __construct function.

	 *

	 * @access public

	 * @param mixed $user_id (default: null)

	 * @param mixed $group (default: null)

	 * @return void

	 */

	public function __construct( $user_id = null, $group = null ){



		// Set the group prefix as Private groups plugin: group

		$this->group_prefix = 'group';

		// Set the usermeta name as Private groups plugin: private_group

		$this->user_meta_name = 'private_group';



		/**validate user_id and group

		 *

		 * user_id must be an integer

		 *

		 * group must be:

		 * a string

		 * longer then 5 char

		 * first 5 chars must be: group

		 * others chars must be numeric. i.e. 1

		 *

		 * if validation fail, the class do nothing

		 *

		 */

		$this->validate_user_id( $user_id );



		$this->validate_group( $group );

	}





	/**

	 * add function.

	 *

	 * Instantiate the class: $bbp_private_groups_adds = new rpg_Bbp_Private_Groups_Adds( $user_id , $group )

	 * Then, to add a new group to a user call the add method: $bbp_private_groups_adds->add();

	 *

	 * @access public

	 * @return void

	 */

	public function add(){



		if ( false == $this->user_id || false == $this->group )

			return;



		$user_meta = get_user_meta( $this->user_id, $this->user_meta_name, true );



		if ( ! empty( $user_meta) ){



			$explode = array_filter( explode( '*', $user_meta ) );



			if ( is_array( $explode ) ){



				if ( ! in_array( $this->group, $explode ) ){



					$explode[] = $this->group;



					$new_groups = '*';



					foreach ( $explode as $x ){



						$new_groups .= $x . '*';



					}



					update_user_meta( $this->user_id, $this->user_meta_name, $new_groups );

				}

			}



		} else {



			// User meta $this->user_meta_name is empty.

			// NO groups for this user

			// add user meta for the selected group: *groupINTnumber* i.e. group1



			$new_groups = '*' . $this->group . '*';



			update_user_meta( $this->user_id, $this->user_meta_name, $new_groups );

		}

	}





	/**

	 * remove function.

	 *

	 * Instantiate the class: $bbp_private_groups_adds = new rpg_Bbp_Private_Groups_Adds( $user_id , $group )

	 * Then, to remove a group from a user call the remove method: $bbp_private_groups_adds->remove();

	 *

	 *

	 * @access public

	 * @return void

	 */

	public function remove(){



		if ( false == $this->user_id || false == $this->group )

			return;



		$user_meta = get_user_meta( $this->user_id, $this->user_meta_name, true );



		if ( ! empty( $user_meta) ){



			$explode = array_filter( explode( '*', $user_meta ) );



			if ( is_array( $explode ) ){



				if ( in_array( $this->group, $explode ) && count( $explode ) > 1 ){



					$new_groups = '*';



					foreach ( $explode as $x ){



						if ( $this->group != $x ){



							$new_groups .= $x . '*';



						}



					}



					update_user_meta( $this->user_id, $this->user_meta_name, $new_groups );



				} elseif  ( in_array( $this->group, $explode ) && count( $explode ) == 1 ){



					delete_user_meta( $this->user_id, $this->user_meta_name );

				}

			}



		}



	}





	/**

	 * validate_user_id function.

	 *

	 * @access private

	 * @param mixed $user_id

	 * @return void

	 */

	private function validate_user_id( $user_id ){



		$this->user_id = false;



		if ( ! is_numeric( $user_id ) ){

			$this->errors[] = '$user_id is not numeric';

			return;

		}



		$this->user_id = (int) $user_id;



	}





	/**

	 * validate_group function.

	 *

	 * @access private

	 * @param mixed $group

	 * @return void

	 */

	private function validate_group( $group ){



		$this->group = false;



		if ( ! is_string( $group ) ){

			$this->errors[] = '$group is not a string';

			return;

		}



		if ( 6 > strlen( $group ) ){

			$this->errors[] = '$group is too short';

			return;

		}



		if ( substr( strtolower( $group ), 0, 5 ) != $this->group_prefix ){

			$this->errors[] = '$group prefix is not group';

			return;

		}



		if ( ! is_numeric( substr( $group, 5 ) ) ){

			$this->errors[] = '$group suffix is not numeric';

			return;

		}



		$this->group = strtolower( $group );





	}

}