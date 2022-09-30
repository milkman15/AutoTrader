<?php
/**
 * AT_Init class.
 *
 */

/**
 * Class AT_Init
 *
 * @since 3.8.5
 */
class AT_Init
{
    public function scheduler_admin_actions()
    {
        add_options_page( 'Dump-It Scheduling', 'Dump-It Schedule', 'Administrator', 'Dump-It_Master_Schedule' );
    }
}
