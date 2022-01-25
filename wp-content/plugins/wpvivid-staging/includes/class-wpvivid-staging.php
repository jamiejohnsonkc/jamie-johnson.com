<?php
if (!defined('WPVIVID_STAGING_PLUGIN_DIR'))
{
    die;
}

if ( ! class_exists( 'WP_List_Table' ) )
{
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WPvivid_Staging_List_Ex extends WP_List_Table
{
    public $list;
    public $page_num;
    public $parent;

    public function __construct( $args = array() )
    {
        global $wpdb;
        parent::__construct(
            array(
                'plural' => 'staging',
                'screen' => 'staging',
            )
        );
    }

    public function set_parent($parent)
    {
        $this->parent=$parent;
    }

    public function set_list($list)
    {
        $this->list=$list;
    }

    protected function get_table_classes() {
        return array( 'widefat', 'plugins', $this->_args['plural'] );
    }

    public function print_column_headers( $with_id = true )
    {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        foreach ( $columns as $column_key => $column_display_name ) {
            $class = array( 'manage-column', "column-$column_key" );

            if ( in_array( $column_key, $hidden ) ) {
                $class[] = 'hidden';
            }

            if ( $column_key === $primary )
            {
                $class[] = 'column-primary';
            }

            $tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
            $scope = ( 'th' === $tag ) ? 'scope="col"' : '';
            $id    = $with_id ? "id='$column_key'" : '';

            if ( ! empty( $class ) ) {
                $class = "class='" . join( ' ', $class ) . "'";
            }

            echo "<$tag $scope $id $class>$column_display_name</$tag>";
        }
    }

    public function get_columns()
    {
        $posts_columns = array();

        $posts_columns['pic']  = _('');
        $posts_columns['info'] = _('');

        return $posts_columns;
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array('pic', 'info');
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $total_items =sizeof($this->list);
    }

    public function has_items()
    {
        return !empty($this->list);
    }

    protected function _column_pic( $item, $classes, $data, $primary )
    {
        if(isset($item['site']['fresh_install']))
        {
            $url=esc_url(WPVIVID_STAGING_PLUGIN_URL.'includes/images/Fresh-list.png');
        }
        else
        {
            $url=esc_url(WPVIVID_STAGING_PLUGIN_URL.'includes/images/living-site.png');
        }

        echo '<td class="column-primary" style="margin: 10px;">
                    <div>
                          <div style="margin:auto; width:100px; height:100px; right:50%;">
                            <img src="'.$url.'">
                          </div>
                          <div class="'.esc_attr($item['id']).'" style="margin-top:10px;">
                            <div class="wpvivid-delete-staging-site" style="margin: auto;width: 70px;background-color:#f1f1f1; padding-top:4px;padding-bottom:4px; cursor:pointer;text-align:center;" title="Delete the stating site">Delete</div>
                          </div>           
                     </div>
              </td>';
    }

    protected function _column_info( $item, $classes, $data, $primary ){
        $home_url = home_url();
        global $wpdb;
        $home_url_sql = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name = %s", 'home' ) );
        foreach ( $home_url_sql as $home ){
            $home_url = $home->option_value;
        }
        $home_url = untrailingslashit($home_url);

        $admin_url  = apply_filters('wpvividstg_get_admin_url', '');
        if(isset($item['site']['mu_single']))
        {
            $admin_url =admin_url();
        }
        $admin_name = str_replace($home_url, '', $admin_url);
        $admin_name = trim($admin_name, '/');

        if(isset($item['site']['prefix']) && !empty($item['site']['prefix'])){
            $prefix = $item['site']['prefix'];
            if(isset($item['site']['db_connect']['dbname']) && !empty($item['site']['db_connect']['dbname'])){
                $db_name = $item['site']['db_connect']['dbname'];
            }
            else{
                $db_name = DB_NAME;
            }
        }
        else{
            $prefix = 'N/A';
            $db_name = 'N/A';
        }
        if(isset($item['site']['path']) && !empty($item['site']['path'])){
            $site_dir = $item['site']['path'];
        }
        else{
            $site_dir = 'N/A';
        }
        if(isset($item['site']['home_url']) && !empty($item['site']['home_url'])){
            $site_url = esc_url($item['site']['home_url']);
            $admin_url = esc_url($item['site']['home_url'].'/'.$admin_name.'/');
            $site_url_link = '<a href="'.esc_url($site_url).'" target="_blank">'.$site_url.'</a>';
            $admin_url_link = '<a href="'.esc_url($admin_url).'" target="_blank">'.$admin_url.'</a>';
        }
        else{
            $site_url_link = 'N/A';
            $admin_url_link = 'N/A';
        }

        if(isset($item['site']['fresh_install']))
        {
            $copy_btn='Copy the Fresh Install to Live';
            $update_btn='Update the Fresh Install';
            $site_url='Fresh Install URL';
            $admin_url='Fresh Install Admin URL';
            $tip_text='Tips: Click the \'Copy the Fresh Install to Live\' button above to migrate the fresh install to your live site. Click the \'Update the Fresh Install\' button to update the live site to the fresh install.';
            $class_btn='fresh-install';
        }
        else
        {
            $copy_btn='Copy the Staging Site to Live';
            $update_btn='Update the Staging Site';
            $site_url='Staging Site URL';
            $admin_url='Staging Site Admin URL';
            $tip_text='Tips: Click the \'Copy the Staging Site to Live\' button above to migrate the staging site to your live site. Click the \'Update the Staging Site\' button to update the live site to the staging site.';
            $class_btn='staging-site';
        }

        if(isset($item['site']['mu_single']) && $item['site']['mu_single'] == true){
            $mu_single_class = 'mu-single';
        }
        else{
            $mu_single_class = '';
        }

        echo '<td class="column-description desc" colspan="2">
                        <div style="border-left:4px solid #00a0d2;padding-left:10px;float:left;">
                            <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>'.$site_url.':</strong></span><span class="wpvivid-element-space-right">'.$site_url_link.'</span></div>
                            <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>'.$admin_url.':</strong></span><span class="wpvivid-element-space-right">'.$admin_url_link.'</span></div>
                        </div>
                        <div style="clear:both"></div>
                        <div style="border-left:4px solid #00a0d2;padding-left:10px;float:left;">
                            <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>Database:</strong></span><span class="wpvivid-element-space-right">'.__($db_name).'</span></div>
                            <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>Table Prefix:</strong></span><span class="wpvivid-element-space-right">'.__($prefix).'</span></div>
                            <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>Site Directory:</strong></span><span class="wpvivid-element-space-right">'.__($site_dir).'</span></div>
                        </div>
                        <div style="clear:both"></div>
                        <div class="wpvivid-copy-staging-to-live-block '.$class_btn.' '.$mu_single_class.'" style="margin-top: 10px;">
                            <div>
                                <input class="button-primary wpvivid-copy-staging-to-live '.$class_btn.' '.$mu_single_class.'" type="button" value="'.$copy_btn.'" style="margin-right: 10px;" />
                                <input class="button-primary wpvivid-update-live-to-staging '.$class_btn.' '.$mu_single_class.'" type="button" value="'.$update_btn.'" />
                            </div>
                            <div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>'.$tip_text.'</span></div>
                        </div>
                    </td>';
    }

    public function display_rows()
    {
        $this->_display_rows( $this->list );
    }

    private function _display_rows( $list )
    {
        foreach ( $list as $key=>$item)
        {
            $item['id']=$key;
            $this->single_row($item);
        }
    }

    public function single_row($item)
    {
        if(isset($item['site']['path']) && !empty($item['site']['path'])){
            $staging_site_name = basename($item['site']['path']);
        }
        else{
            $staging_site_name = 'N/A';
        }

        if(isset($item['site']['fresh_install']))
        {
            $text='Fresh Install Name';
        }
        else
        {
            $text='Staging Site Name';
        }

        if(isset($item['db_connect']['old_site_url']))
        {
            $live_domain = $item['db_connect']['old_site_url'];
        }
        else{
            $live_domain = 'N/A';
        }

        ?>
        <tr class="<?php echo $item['id']; ?>">
            <td class="column-primary" style="border-top:1px solid #f1f1f1; border-bottom:1px solid #f1f1f1;" colspan="3" >
                <span><strong><?php echo $text; ?>: </strong></span><span><?php echo _($staging_site_name); ?></span>
                <?php
                if(isset($item['site']['mu_single']))
                {
                    $site_id=$item['site']['mu_single_site_id'];
                    $site_url=get_site_url($site_id);
                    ?>
                    <span style="margin-left: 20px;"><strong>Live Site: </strong></span><span><?php echo _($site_url); ?></span>
                    <?php
                }
                else{
                    ?>
                    <span style="margin-left: 20px;"><strong>Live Site: </strong></span><span><?php echo $live_domain; ?></span>
                    <?php
                }
                ?>
            </td>
        </tr>
        <tr id="<?php echo $item['id']; ?>" class="<?php echo $item['id']; ?>">
            <?php $this->single_row_columns( $item ); ?>
        </tr>
        <?php
    }

    public function display() {
        $singular = $this->_args['singular'];

        $this->screen->render_screen_reader_content( 'heading_list' );
        ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" style="border: 1px solid #f1f1f1; border-top: none;">
            <thead>
            <tr>
                <?php $this->print_column_headers(); ?>
            </tr>
            </thead>

            <tbody id="the-list"
                <?php
                if ( $singular ) {
                    echo " data-wp-lists='list:$singular'";
                }
                ?>
            >
            <?php $this->display_rows_or_placeholder(); ?>
            </tbody>

            <tfoot>
            <tr>
                <?php $this->print_column_headers( false ); ?>
            </tr>
            </tfoot>

        </table>
        <?php
    }

    public function display_js()
    {
        ?>
        <script>

        </script>
        <?php
    }
}

class WPvivid_Staging_MU_Site_List extends WP_List_Table
{
    public $list;
    public $type;
    public $page_num;
    public $parent;

    public function __construct( $args = array() )
    {
        global $wpdb;
        parent::__construct(
            array(
                'plural' => 'staging_mu_site',
                'screen' => 'staging_mu_site',
            )
        );
    }

    public function set_parent($parent)
    {
        $this->parent=$parent;
    }

    public function set_list($list,$type,$page_num=1)
    {
        $this->list=$list;
        $this->type=$type;
        $this->page_num=$page_num;
    }

    protected function get_table_classes()
    {
        return array( 'widefat striped' );
    }

    public function print_column_headers( $with_id = true )
    {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        if (!empty($columns['cb']))
        {
            static $cb_counter = 1;
            $columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __('Select All') . '</label>'
                . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox"/>';
            $cb_counter++;
        }

        foreach ( $columns as $column_key => $column_display_name )
        {
            $class = array( 'manage-column', "column-$column_key" );

            if ( in_array( $column_key, $hidden ) )
            {
                $class[] = 'hidden';
            }

            if ( $column_key === $primary )
            {
                $class[] = 'column-primary';
            }

            if ( $column_key === 'cb' )
            {
                $class[] = 'check-column';
            }

            $tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
            $scope = ( 'th' === $tag ) ? 'scope="col"' : '';
            $id    = $with_id ? "id='$column_key'" : '';

            if ( ! empty( $class ) )
            {
                $class = "class='" . join( ' ', $class ) . "'";
            }

            echo "<$tag $scope $id $class>$column_display_name</$tag>";
        }
    }

    public function get_columns()
    {
        $sites_columns = array(
            'cb'          => '<input type="checkbox" />',
            'blogname'    => __( 'Subsite URL' ),
            'tables_folders'=>__( 'Subsite Tables/Folders' ),
            'title' => __( 'Subsite Title' ),
            'description'  => __( 'Subsite Description')
        );

        return $sites_columns;
    }

    public function get_pagenum()
    {
        if($this->page_num=='first')
        {
            $this->page_num=1;
        }
        else if($this->page_num=='last')
        {
            $this->page_num=$this->_pagination_args['total_pages'];
        }
        $pagenum = $this->page_num ? $this->page_num : 0;

        if ( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] )
        {
            $pagenum = $this->_pagination_args['total_pages'];
        }

        return max( 1, $pagenum );
    }

    public function column_cb( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        $blogname = get_object_vars($subsite)["domain"].get_object_vars($subsite)["path"];
        ?>
        <label class="screen-reader-text" for="blog_<?php echo $subsite_id; ?>">
            <?php
            printf( __( 'Select %s' ), $blogname );
            ?>
        </label>
        <input type="checkbox" name="<?php echo esc_attr( $this->type ); ?>" value="<?php echo esc_attr( $subsite_id ); ?>" checked />
        <?php
    }

    public function column_id( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        echo $subsite_id;
    }

    public function column_blogname( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        $blogname    = untrailingslashit( get_object_vars($subsite)['domain'] . get_object_vars($subsite)['path'] );
        ?>
        <strong>
            <a href="<?php echo esc_url( network_admin_url( 'site-info.php?id=' .$subsite_id ) ); ?>" class="edit"><?php echo $blogname; ?></a>
        </strong>
        <?php
    }

    public function column_tables_folders( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        $disable='';
        /*if( $this->type=='copy_mu_site')
        {
            $disable='';
        }
        else
        {
            $disable='disabled';
        }*/
        ?>
        <label>
            <input type="checkbox" name="<?php echo esc_attr( $this->type ); ?>_tables" value="<?php echo esc_attr( $subsite_id ); ?>" checked <?php echo esc_attr( $disable ); ?>/>
            Tables /
        </label>
        <label>
            <input type="checkbox" name="<?php echo esc_attr( $this->type ); ?>_folders" value="<?php echo esc_attr( $subsite_id ); ?>" checked <?php echo esc_attr( $disable ); ?>/>
            Folders
        </label>
        <?php
    }

    public function column_title( $subsite )
    {
        switch_to_blog( get_object_vars($subsite)["blog_id"] );
        echo ( get_option( 'blogname' ) ) ;
        restore_current_blog();
    }

    public function column_description( $subsite ) {
        switch_to_blog( get_object_vars($subsite)["blog_id"] );
        echo (  get_option( 'blogdescription ' ) ) ;
        restore_current_blog();
    }

    public function has_items()
    {
        return !empty($this->list);
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $total_items =sizeof($this->list);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page'    => 10,
            )
        );
    }

    public function display_rows()
    {
        $this->_display_rows( $this->list );
    }

    private function _display_rows( $list )
    {
        $page=$this->get_pagenum();

        $page_list=$list;
        $temp_page_list=array();

        $count=0;
        while ( $count<$page )
        {
            $temp_page_list = array_splice( $page_list, 0, 10);
            $count++;
        }

        foreach ( $temp_page_list as $key=>$item)
        {
            $this->single_row($item);
        }
    }

    public function single_row($item)
    {
        ?>
        <tr>
            <?php $this->single_row_columns( $item ); ?>
        </tr>
        <?php
    }

    protected function pagination( $which )
    {
        if ( empty( $this->_pagination_args ) )
        {
            return;
        }

        $total_items     = $this->_pagination_args['total_items'];
        $total_pages     = $this->_pagination_args['total_pages'];
        $infinite_scroll = false;
        if ( isset( $this->_pagination_args['infinite_scroll'] ) )
        {
            $infinite_scroll = $this->_pagination_args['infinite_scroll'];
        }

        if ( 'top' === $which && $total_pages > 1 )
        {
            $this->screen->render_screen_reader_content( 'heading_pagination' );
        }

        $output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

        $current              = $this->get_pagenum();

        $page_links = array();

        $total_pages_before = '<span class="paging-input">';
        $total_pages_after  = '</span></span>';

        $disable_first = $disable_last = $disable_prev = $disable_next = false;

        if ( $current == 1 ) {
            $disable_first = true;
            $disable_prev  = true;
        }
        if ( $current == 2 ) {
            $disable_first = true;
        }
        if ( $current == $total_pages ) {
            $disable_last = true;
            $disable_next = true;
        }
        if ( $current == $total_pages - 1 ) {
            $disable_last = true;
        }

        if ( $disable_first ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<div class='first-page button'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></div>",
                __( 'First page' ),
                '&laquo;'
            );
        }

        if ( $disable_prev ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<div class='prev-page button' value='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></div>",
                $current,
                __( 'Previous page' ),
                '&lsaquo;'
            );
        }

        if ( 'bottom' === $which ) {
            $html_current_page  = $current;
            $total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
        } else {
            $html_current_page = sprintf(
                "%s<input class='current-page' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
                $current,
                strlen( $total_pages )
            );
        }
        $html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
        $page_links[]     = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

        if ( $disable_next ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<div class='next-page button' value='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></div>",
                $current,
                __( 'Next page' ),
                '&rsaquo;'
            );
        }

        if ( $disable_last ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<div class='last-page button'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></div>",
                __( 'Last page' ),
                '&raquo;'
            );
        }

        $pagination_links_class = 'pagination-links';
        if ( ! empty( $infinite_scroll ) ) {
            $pagination_links_class .= ' hide-if-js';
        }
        $output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

        if ( $total_pages ) {
            $page_class = $total_pages < 2 ? ' one-page' : '';
        } else {
            $page_class = ' no-pages';
        }
        $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

        echo $this->_pagination;
    }

    protected function display_tablenav( $which ) {
        $css_type = '';
        if ( 'top' === $which ) {
            wp_nonce_field( 'bulk-' . $this->_args['plural'] );
            $css_type = 'margin: 0 0 10px 0';
        }
        else if( 'bottom' === $which ) {
            $css_type = 'margin: 10px 0 0 0';
        }

        $total_pages     = $this->_pagination_args['total_pages'];
        if ( $total_pages >1)
        {
            ?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>" style="<?php esc_attr_e($css_type); ?>">
                <?php
                $this->extra_tablenav( $which );
                $this->pagination( $which );
                ?>

                <br class="clear" />
            </div>
            <?php
        }
    }

    public function display() {
        $singular = $this->_args['singular'];

        $this->display_tablenav( 'top' );

        $this->screen->render_screen_reader_content( 'heading_list' );
        ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" >
            <thead>
            <tr>
                <?php $this->print_column_headers(); ?>
            </tr>
            </thead>

            <tbody id="the-list"
                <?php
                if ( $singular ) {
                    echo " data-wp-lists='list:$singular'";
                }
                ?>
            >
            <?php $this->display_rows_or_placeholder(); ?>
            </tbody>

            <tfoot>
            <tr>
                <?php $this->print_column_headers( false ); ?>
            </tr>
            </tfoot>

        </table>
        <?php
    }
}

class WPvivid_Staging_MU_Single_Site_List extends WP_List_Table
{
    public $list;
    public $type;
    public $page_num;
    public $parent;

    public function __construct( $args = array() )
    {
        global $wpdb;
        parent::__construct(
            array(
                'plural' => 'staging_mu_site',
                'screen' => 'staging_mu_site',
            )
        );
    }

    public function set_parent($parent)
    {
        $this->parent=$parent;
    }

    public function set_list($list,$type,$page_num=1)
    {
        $this->list=$list;
        $this->type=$type;
        $this->page_num=$page_num;
    }

    protected function get_table_classes()
    {
        return array( 'widefat striped' );
    }

    public function print_column_headers( $with_id = true )
    {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        /*
        if (!empty($columns['cb']))
        {
            static $cb_counter = 1;
            $columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __('Select All') . '</label>'
                . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox"/>';
            $cb_counter++;
        }
        */

        foreach ( $columns as $column_key => $column_display_name )
        {

            $class = array( 'manage-column', "column-$column_key" );

            if ( in_array( $column_key, $hidden ) )
            {
                $class[] = 'hidden';
            }


            if ( $column_key === $primary )
            {
                $class[] = 'column-primary';
            }

            if ( $column_key === 'cb' )
            {
                //$class[] = 'check-column';
            }
            $tag='th';
            //$tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
            $scope = ( 'th' === $tag ) ? 'scope="col"' : '';
            $id    = $with_id ? "id='$column_key'" : '';

            if ( ! empty( $class ) )
            {
                $class = "class='" . join( ' ', $class ) . "'";
            }

            echo "<$tag $scope $id $class>$column_display_name</$tag>";
        }
    }

    public function get_columns()
    {
        $sites_columns = array(
            'cb'          => __( ' ' ),
            'blogname'    => __( 'Subsite URL' ),
            //'tables_folders'=>__( 'Subsite Tables/Folders' ),
            'title' => __( 'Subsite Title' ),
            'description'  => __( 'Subsite Description')
        );

        return $sites_columns;
    }

    public function get_pagenum()
    {
        if($this->page_num=='first')
        {
            $this->page_num=1;
        }
        else if($this->page_num=='last')
        {
            $this->page_num=$this->_pagination_args['total_pages'];
        }
        $pagenum = $this->page_num ? $this->page_num : 0;

        if ( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] )
        {
            $pagenum = $this->_pagination_args['total_pages'];
        }

        return max( 1, $pagenum );
    }

    public function column_cb( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        $blogname = get_object_vars($subsite)["domain"].get_object_vars($subsite)["path"];
        ?>
        <label class="screen-reader-text" for="blog_<?php echo $subsite_id; ?>">
            <?php
            printf( __( 'Select %s' ), $blogname );
            ?>
        </label>
        <input type="checkbox" name="<?php echo esc_attr( $this->type ); ?>" value="<?php echo esc_attr( $subsite_id ); ?>" />
        <?php
    }

    public function column_id( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        echo $subsite_id;
    }

    public function column_blogname( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        $blogname    = untrailingslashit( get_object_vars($subsite)['domain'] . get_object_vars($subsite)['path'] );
        ?>
        <strong>
            <a href="<?php echo esc_url( network_admin_url( 'site-info.php?id=' .$subsite_id ) ); ?>" class="edit"><?php echo $blogname; ?></a>
        </strong>
        <?php
    }

    public function column_tables_folders( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        $disable='';
        /*if( $this->type=='copy_mu_site')
        {
            $disable='';
        }
        else
        {
            $disable='disabled';
        }*/
        ?>
        <label>
            <input type="checkbox" name="<?php echo esc_attr( $this->type ); ?>_tables" value="<?php echo esc_attr( $subsite_id ); ?>" <?php echo esc_attr( $disable ); ?>/>
            Tables /
        </label>
        <label>
            <input type="checkbox" name="<?php echo esc_attr( $this->type ); ?>_folders" value="<?php echo esc_attr( $subsite_id ); ?>" <?php echo esc_attr( $disable ); ?>/>
            Folders
        </label>
        <?php
    }

    public function column_title( $subsite )
    {
        switch_to_blog( get_object_vars($subsite)["blog_id"] );
        echo ( get_option( 'blogname' ) ) ;
        restore_current_blog();
    }

    public function column_description( $subsite ) {
        switch_to_blog( get_object_vars($subsite)["blog_id"] );
        echo (  get_option( 'blogdescription ' ) ) ;
        restore_current_blog();
    }

    public function has_items()
    {
        return !empty($this->list);
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $total_items =sizeof($this->list);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page'    => 10,
            )
        );
    }

    public function display_rows()
    {
        $this->_display_rows( $this->list );
    }

    private function _display_rows( $list )
    {
        $page=$this->get_pagenum();

        $page_list=$list;
        $temp_page_list=array();

        $count=0;
        while ( $count<$page )
        {
            $temp_page_list = array_splice( $page_list, 0, 10);
            $count++;
        }

        foreach ( $temp_page_list as $key=>$item)
        {
            $this->single_row($item);
        }
    }

    public function single_row($item)
    {
        ?>
        <tr>
            <?php $this->single_row_columns( $item ); ?>
        </tr>
        <?php
    }

    protected function pagination( $which )
    {
        if ( empty( $this->_pagination_args ) )
        {
            return;
        }

        $total_items     = $this->_pagination_args['total_items'];
        $total_pages     = $this->_pagination_args['total_pages'];
        $infinite_scroll = false;
        if ( isset( $this->_pagination_args['infinite_scroll'] ) )
        {
            $infinite_scroll = $this->_pagination_args['infinite_scroll'];
        }

        if ( 'top' === $which && $total_pages > 1 )
        {
            $this->screen->render_screen_reader_content( 'heading_pagination' );
        }

        $output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

        $current              = $this->get_pagenum();

        $page_links = array();

        $total_pages_before = '<span class="paging-input">';
        $total_pages_after  = '</span></span>';

        $disable_first = $disable_last = $disable_prev = $disable_next = false;

        if ( $current == 1 ) {
            $disable_first = true;
            $disable_prev  = true;
        }
        if ( $current == 2 ) {
            $disable_first = true;
        }
        if ( $current == $total_pages ) {
            $disable_last = true;
            $disable_next = true;
        }
        if ( $current == $total_pages - 1 ) {
            $disable_last = true;
        }

        if ( $disable_first ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<div class='first-page button'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></div>",
                __( 'First page' ),
                '&laquo;'
            );
        }

        if ( $disable_prev ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<div class='prev-page button' value='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></div>",
                $current,
                __( 'Previous page' ),
                '&lsaquo;'
            );
        }

        if ( 'bottom' === $which ) {
            $html_current_page  = $current;
            $total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
        } else {
            $html_current_page = sprintf(
                "%s<input class='current-page'  type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label  class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
                $current,
                strlen( $total_pages )
            );
        }
        $html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
        $page_links[]     = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

        if ( $disable_next ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<div class='next-page button' value='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></div>",
                $current,
                __( 'Next page' ),
                '&rsaquo;'
            );
        }

        if ( $disable_last ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<div class='last-page button'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></div>",
                __( 'Last page' ),
                '&raquo;'
            );
        }

        $pagination_links_class = 'pagination-links';
        if ( ! empty( $infinite_scroll ) ) {
            $pagination_links_class .= ' hide-if-js';
        }
        $output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

        if ( $total_pages ) {
            $page_class = $total_pages < 2 ? ' one-page' : '';
        } else {
            $page_class = ' no-pages';
        }
        $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

        echo $this->_pagination;
    }

    protected function display_tablenav( $which ) {
        $css_type = '';
        if ( 'top' === $which ) {
            wp_nonce_field( 'bulk-' . $this->_args['plural'] );
            $css_type = 'margin: 0 0 10px 0';
        }
        else if( 'bottom' === $which ) {
            $css_type = 'margin: 10px 0 0 0';
        }

        $total_pages     = $this->_pagination_args['total_pages'];
        if ( $total_pages >1)
        {
            ?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>" style="<?php esc_attr_e($css_type); ?>">
                <?php
                $this->extra_tablenav( $which );
                $this->pagination( $which );
                ?>

                <br class="clear" />
            </div>
            <?php
        }
    }

    public function display() {
        $singular = $this->_args['singular'];

        $this->display_tablenav( 'top' );

        $this->screen->render_screen_reader_content( 'heading_list' );
        ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" >
            <thead>
            <tr>
                <?php $this->print_column_headers(); ?>
            </tr>
            </thead>

            <tbody id="the-list"
                <?php
                if ( $singular ) {
                    echo " data-wp-lists='list:$singular'";
                }
                ?>
            >
            <?php $this->display_rows_or_placeholder(); ?>
            </tbody>

            <tfoot>
            <tr>
                <?php $this->print_column_headers( false ); ?>
            </tr>
            </tfoot>

        </table>
        <?php
    }
}

class WPvivid_Custom_MU_Staging_List_Ex
{
    public $parent_id;
    public $is_staging_site   = false;
    public $is_sync_site      = false;
    public $staging_home_path = false;
    public $custom_uploads_path;
    public $custom_content_path;
    public $custom_additional_file_path;

    public function __construct(){

    }

    public function set_parent_id($parent_id){
        $this->parent_id = $parent_id;
    }

    public function set_staging_home_path($is_staging_site=false, $is_sync_site=false, $staging_home_path=false){
        $this->is_staging_site   = $is_staging_site;
        $this->is_sync_site      = $is_sync_site;
        $this->staging_home_path = $staging_home_path;
    }

    public function display_rows()
    {
        $core_check = 'checked';
        $database_check = 'checked';
        $database_text_style = 'pointer-events: auto; opacity: 1;';
        $themes_check = 'checked';
        $plugins_check = 'checked';
        $themes_plugins_check = 'checked';
        $themes_plugins_text_style = 'pointer-events: auto; opacity: 1;';
        $uploads_check = 'checked';
        $uploads_text_style = 'pointer-events: auto; opacity: 1;';
        $content_check = 'checked';
        $content_text_style = 'pointer-events: auto; opacity: 1;';
        $additional_file_check = '';
        $additional_file_text_style = 'pointer-events: none; opacity: 0.4;';
        $upload_extension = '';
        $content_extension = '';
        $additional_file_extension = '';

        $db_descript = 'All the tables in the WordPress MU database except for subsites tables.';
        $uploads_descript = 'The folder where images and media files of the main site are stored by default. All files will be copied to the staging site by default. You can exclude folders you do not want to copy.';
        $core_descript = 'These are the essential files for creating a staging site.';
        $themes_plugins_descript = 'All the plugins and themes files used by the MU network. The activated plugins and themes will be copied to the staging site by default. A child theme must be copied if it exists.';
        $contents_descript = '<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to copy to the staging site, except for the wp-content/uploads folder.';
        $additional_file_descript = '<strong style="text-decoration:underline;"><i>Include</i></strong> additional files or folders you want to copy to the staging site.';

        ?>
        <table class="wp-list-table widefat plugins wpvivid-custom-table">
            <tbody>
            <!-------- core -------->
            <tr>
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" checked disabled/>
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-wordpress-core">WordPress Core</td>
                <td class="column-description desc"><?php _e($core_descript); ?></td>
            </tr>
            <!-------- database -------->
            <tr style="cursor:pointer;">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" checked disabled/>
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-database-detail">Database</td>
                <td class="column-description desc wpvivid-handle-database-detail database-desc">
                    <?php _e($db_descript); ?>
                </td>
            </tr>
            <!-------- uploads -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-uploads-check" checked disabled/>
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-uploads-detail">wp-content/uploads</td>
                <td class="column-description desc wpvivid-handle-uploads-detail uploads-desc"><?php _e($uploads_descript); ?></td>
                <th class="wpvivid-handle-uploads-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-uploads-detail wpvivid-close" style="<?php esc_attr_e($uploads_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary">
                    <table class="wp-list-table widefat plugins" style="width:100%;">
                        <thead>
                        <tr>
                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                <label class="wpvivid-refresh-tree wpvivid-refresh-uploads-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder Tree</label>
                            </th>
                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                <div class="wpvivid-custom-uploads-tree">
                                    <div class="wpvivid-custom-tree wpvivid-custom-uploads-tree-info"></div>
                                </div>
                            </td>
                            <td class="wpvivid-custom-uploads-right">
                                <div class="wpvivid-custom-uploads-table wpvivid-custom-exclude-uploads-list">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <div>
                                    <div style="float: left; margin-right: 10px;">
                                        <input class="button-primary wpvivid-exclude-uploads-folder-btn" type="submit" value="Exclude Folders" disabled />
                                    </div>
                                    <small>
                                        <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                            <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                        </div>
                                    </small>
                                    <div style="clear: both;"></div>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                        <div style="clear:both;"></div>
                    </table>
                    <div style="margin-top: 10px;">
                        <div style="float: left; margin-right: 10px;">
                            <input type="text" class="regular-text wpvivid-uploads-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="<?php esc_attr_e($upload_extension); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,'')"/>
                            <input type="button" class="wpvivid-uploads-extension-rule-btn" value="Save" />
                        </div>
                        <small>
                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                            </div>
                        </small>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            <!-------- themes and plugins -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-themes-plugins-check" checked disabled/>
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-themes-plugins-detail">Themes and Plugins</td>
                <td class="column-description desc wpvivid-handle-themes-plugins-detail themes-plugins-desc">
                    <?php _e($themes_plugins_descript); ?>
                </td>
                <th class="wpvivid-handle-themes-plugins-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-themes-plugins-detail wpvivid-close" style="pointer-events: auto; opacity: 1; display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary wpvivid-custom-themes-plugins-info">
                    <div class="spinner" style="margin: 0 5px 10px 0; float: left;"></div>
                    <div style="float: left;">Archieving themes and plugins</div>
                    <div style="clear: both;"></div>
                </td>
            </tr>
            <!-------- content -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-content-check" checked disabled/>
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-content-detail">wp-content</td>
                <td class="column-description desc wpvivid-handle-content-detail content-desc"><?php _e($contents_descript); ?></td>
                <th class="wpvivid-handle-content-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-content-detail wpvivid-close" style="<?php esc_attr_e($content_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary">
                    <table class="wp-list-table widefat plugins" style="width:100%;">
                        <thead>
                        <tr>
                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                <label class="wpvivid-refresh-tree wpvivid-refresh-content-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder Tree</label>
                            </th>
                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                <div class="wpvivid-custom-uploads-tree">
                                    <div class="wpvivid-custom-tree wpvivid-custom-content-tree-info"></div>
                                </div>
                            </td>
                            <td class="wpvivid-custom-uploads-right">
                                <div class="wpvivid-custom-uploads-table wpvivid-custom-exclude-content-list">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <div style="float: left; margin-right: 10px;">
                                    <input class="button-primary wpvivid-exclude-content-folder-btn" type="submit" value="Exclude Folders" disabled />
                                </div>
                                <small>
                                    <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                        <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                    </div>
                                </small>
                                <div style="clear: both;"></div>
                            </td>
                        </tr>
                        </tfoot>
                        <div style="clear:both;"></div>
                    </table>
                    <div style="margin-top: 10px;">
                        <div style="float: left; margin-right: 10px;">
                            <input type="text" class="regular-text wpvivid-content-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="<?php esc_attr_e($content_extension); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,'')"/>
                            <input type="button" class="wpvivid-content-extension-rule-btn" value="Save" />
                        </div>
                        <small>
                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                            </div>
                        </small>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            <!-------- additional files -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-additional-file-check" <?php esc_attr_e($additional_file_check); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-additional-file-detail">Additional Files/Folder</td>
                <td class="column-description desc wpvivid-handle-additional-file-detail additional-file-desc"><?php _e($additional_file_descript); ?></td>
                <th class="wpvivid-handle-additional-file-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-additional-file-detail wpvivid-close" style="<?php esc_attr_e($additional_file_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary">
                    <table class="wp-list-table widefat plugins" style="width:100%;">
                        <thead>
                        <tr>
                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                <label class="wpvivid-refresh-tree wpvivid-refresh-additional-file-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder/File Tree</label>
                            </th>
                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                <div class="wpvivid-custom-uploads-tree">
                                    <div class="wpvivid-custom-tree wpvivid-custom-additional-file-tree-info"></div>
                                </div>
                            </td>
                            <td class="wpvivid-custom-uploads-right">
                                <div class="wpvivid-custom-uploads-table wpvivid-custom-include-additional-file-list">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <div style="float: left; margin-right: 10px;">
                                    <input class="button-primary wpvivid-include-additional-file-btn" type="submit" value="Include folders/files" disabled />
                                </div>
                                <small>
                                    <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                        <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                    </div>
                                </small>
                                <div style="clear: both;"></div>
                            </td>
                        </tr>
                        </tfoot>
                        <div style="clear:both;"></div>
                    </table>
                    <div style="margin-top: 10px;">
                        <div style="float: left; margin-right: 10px;">
                            <input type="text" class="regular-text wpvivid-additional-file-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="<?php esc_attr_e($additional_file_extension); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,'')"/>
                            <input type="button" class="wpvivid-additional-file-extension-rule-btn" value="Save" />
                        </div>
                        <small>
                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                            </div>
                        </small>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function load_js(){
        $upload_dir = wp_upload_dir();
        $upload_path = $this->is_staging_site === false ?  $upload_dir['basedir'] : $this->staging_home_path.'/wp-content/uploads';
        $upload_path = str_replace('\\','/',$upload_path);
        $upload_path = $upload_path.'/';
        $this->custom_uploads_path = $upload_path;

        $content_dir = $this->is_staging_site === false ? WP_CONTENT_DIR : $this->staging_home_path.'/wp-content';
        $content_path = str_replace('\\','/',$content_dir);
        $content_path = $content_path.'/';
        $this->custom_content_path = $content_path;

        $additional_file_path = $this->is_staging_site === false ? str_replace('\\','/',get_home_path()) : str_replace('\\','/',$this->staging_home_path);
        $this->custom_additional_file_path = $additional_file_path;
        ?>
        <script>
            function wpvivid_handle_custom_open_close(obj, sub_obj){
                if(obj.hasClass('wpvivid-close')) {
                    sub_obj.hide();
                    sub_obj.prev().find('details').prop('open', false);
                    sub_obj.removeClass('wpvivid-open');
                    sub_obj.addClass('wpvivid-close');
                    sub_obj.prev().css('background-color', '#fff');
                    obj.prev().css('background-color', '#f1f1f1');
                    obj.prev().find('details').prop('open', true);
                    obj.show();
                    obj.removeClass('wpvivid-close');
                    obj.addClass('wpvivid-open');
                }
                else{
                    obj.hide();
                    obj.prev().css('background-color', '#fff');
                    obj.prev().find('details').prop('open', false);
                    obj.removeClass('wpvivid-open');
                    obj.addClass('wpvivid-close');
                }
            }

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-database-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-themes-plugins-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-themes-plugins-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-uploads-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-uploads-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-content-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-content-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-additional-file-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-additional-file-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-custom-check', function() {
                if (jQuery(this).prop('checked')) {
                    if(!jQuery(this).hasClass('wpvivid-custom-core-check')) {
                        jQuery(jQuery(this).parents('tr').next().get(0)).css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-check').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status) {
                        if (!jQuery(this).hasClass('wpvivid-custom-core-check')) {
                            jQuery(jQuery(this).parents('tr').next().get(0)).css({'pointer-events': 'none', 'opacity': '0.4'});
                        }
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one item under Custom option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-database-table-check', function() {
                if(jQuery(this).prop('checked')){
                    if(jQuery(this).hasClass('wpvivid-database-base-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').prop('checked', true);
                    }
                    else if(jQuery(this).hasClass('wpvivid-database-woo-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').prop('checked', true);
                    }
                    else if(jQuery(this).hasClass('wpvivid-database-other-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    if (jQuery(this).hasClass('wpvivid-database-base-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                    else if (jQuery(this).hasClass('wpvivid-database-woo-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                    else if (jQuery(this).hasClass('wpvivid-database-other-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=base_db][name=Database]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-base-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[name=Database]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-base-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one table type under the Database option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=woo_db][name=Database]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-woo-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[name=Database]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-woo-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one table type under the Database option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=other_db][name=Database]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-other-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[name=Database]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-other-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one table type under the Database option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-themes-plugins-table-check', function(){
                if(jQuery(this).prop('checked')){
                    if(jQuery(this).hasClass('wpvivid-themes-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').prop('checked', true);
                    }
                    else if(jQuery(this).hasClass('wpvivid-plugins-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    if (jQuery(this).hasClass('wpvivid-themes-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                        }
                    }
                    else if (jQuery(this).hasClass('wpvivid-plugins-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                                if(jQuery(this).val() !== 'wpvivid-backuprestore' && jQuery(this).val() !== 'wpvivid-backup-pro'){
                                    jQuery(this).prop('checked', false);
                                }
                            });
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                        }
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=themes][name=Themes]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-themes-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(!check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                    }
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-themes-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=plugins][name=Plugins]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-plugins-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(!check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                    }
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-plugins-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-uploads-extension-rule-btn', function(){
                var value = jQuery(this).prev().val();
                if(value!=='') {
                    wpvivid_update_staging_exclude_extension('upload', value);
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-content-extension-rule-btn', function(){
                var value = jQuery(this).prev().val();
                if(value!=='') {
                    wpvivid_update_staging_exclude_extension('content', value);
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-additional-file-extension-rule-btn', function(){
                var value = jQuery(this).prev().val();
                if(value!=='') {
                    wpvivid_update_staging_exclude_extension('additional_file', value);
                }
            });

            function wpvivid_update_staging_exclude_extension(type, value){
                var ajax_data = {
                    'action': 'wpvividstg_update_staging_exclude_extension',
                    'type': type,
                    'exclude_content': value
                };
                jQuery(this).css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function (data) {
                    jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success') {
                        }
                    }
                    catch (err) {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = wpvivid_output_ajaxerror('saving staging extension', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-custom-li-close', function(){
                jQuery(this).parent().parent().remove();
            });
        </script>
        <?php
    }
}

class WPvivid_Custom_Staging_List_Ex
{
    public $parent_id;
    public $is_staging_site   = false;
    public $staging_home_path = false;
    public $custom_uploads_path;
    public $custom_content_path;
    public $custom_additional_file_path;

    public function __construct(){

    }

    public function set_parent_id($parent_id){
        $this->parent_id = $parent_id;
    }

    public function set_staging_home_path($is_staging_site=false, $staging_home_path=false){
        $this->is_staging_site   = $is_staging_site;
        $this->staging_home_path = $staging_home_path;
    }

    public function display_rows(){
        $core_check = 'checked';
        $database_check = 'checked';
        $database_text_style = 'pointer-events: auto; opacity: 1;';
        $themes_check = 'checked';
        $plugins_check = 'checked';
        $themes_plugins_check = 'checked';
        $themes_plugins_text_style = 'pointer-events: auto; opacity: 1;';
        $uploads_check = 'checked';
        $uploads_text_style = 'pointer-events: auto; opacity: 1;';
        $content_check = 'checked';
        $content_text_style = 'pointer-events: auto; opacity: 1;';
        $additional_file_check = '';
        $additional_file_text_style = 'pointer-events: none; opacity: 0.4;';
        $upload_extension = '';
        $content_extension = '';
        $additional_file_extension = '';
        if($this->is_staging_site){
            $border_css = 'border: 1px solid #f1f1f1;';
            $checkbox_disable = '';
            $core_descript = 'If the staging site and the live site have the same version of WordPress. Then it is not necessary to copy the WordPress core files to the live site.';
            $db_descript = 'It is recommended to copy all tables of the database to the live site.';
            $themes_plugins_descript = 'The activated plugins and themes will be copied to the live site by default. The Child theme must be copied if it exists';
            $uploads_descript = 'Images and media files are stored in the Uploads directory by default. All files are copied to the live site by default. You can exclude folders you do not want to copy.';
            $contents_descript = '<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to copy to the live site, except for the wp-content/uploads folder.';
            $additional_file_descript = '<strong style="text-decoration:underline;"><i>Include</i></strong> additional files or folders you want to copy to the live site.';
        }
        else{
            $border_css = 'border: none;';
            $checkbox_disable = ' disabled';
            $core_descript = 'These are the essential files for creating a staging site.';
            $db_descript = 'The tables created by WordPress are required for the staging site. Database tables created by themes or plugins are optional.';
            $themes_plugins_descript = 'The activated plugins and themes will be copied to a staging site by default. A Child theme must be copied if it exists.';
            $uploads_descript = 'Images and media files are stored in the Uploads directory by default. All files are copied to the staging site by default. You can exclude folders you do not want to copy.';
            $contents_descript = '<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to copy to the staging site, except for the wp-content/uploads folder.';
            $additional_file_descript = '<strong style="text-decoration:underline;"><i>Include</i></strong> additional files or folders you want to copy to the staging site.';
            $options = get_option('wpvivid_staging_history', array());
            if(isset($options['additional_file_check'])) {
                $additional_file_check = $options['additional_file_check'] == '1' ? 'checked' : '';
                $additional_file_text_style = $options['additional_file_check'] == '1' ? 'pointer-events: auto; opacity: 1;' : 'pointer-events: none; opacity: 0.4;';
            }
            if(isset($options['upload_extension']) && !empty($options['upload_extension'])){
                $upload_extension = implode(",", $options['upload_extension']);
            }
            if(isset($options['content_extension']) && !empty($options['content_extension'])){
                $content_extension = implode(",", $options['content_extension']);
            }
            if(isset($options['additional_file_extension']) && !empty($options['additional_file_extension'])){
                $additional_file_extension = implode(",", $options['additional_file_extension']);
            }
        }
        ?>
        <table class="wp-list-table widefat plugins wpvivid-custom-table" style="<?php esc_attr_e($border_css); ?>">
            <tbody>
            <!-------- core -------->
            <tr>
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-core-check" <?php esc_attr_e($core_check.$checkbox_disable); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-wordpress-core">Wordpress Core</td>
                <td class="column-description desc core-desc"><?php _e($core_descript); ?></td>
            </tr>
            <!-------- database -------->
            <tr style="cursor:pointer;">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-database-check" <?php esc_attr_e($database_check.$checkbox_disable); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-database-detail">Database</td>
                <td class="column-description desc wpvivid-handle-database-detail database-desc">
                    <?php _e($db_descript); ?>
                </td>
                <th class="wpvivid-handle-database-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-database-detail wpvivid-close" style="<?php esc_attr_e($database_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary wpvivid-custom-database-info">
                    <div class="spinner" style="margin: 0 5px 10px 0; float: left;"></div>
                    <div style="float: left;">Archieving database tables</div>
                    <div style="clear: both;"></div>
                </td>
            </tr>
            <!-------- themes and plugins -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-themes-plugins-check" <?php esc_attr_e($themes_plugins_check.$checkbox_disable); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-themes-plugins-detail">Themes and Plugins</td>
                <td class="column-description desc wpvivid-handle-themes-plugins-detail themes-plugins-desc">
                    <?php _e($themes_plugins_descript); ?>
                </td>
                <th class="wpvivid-handle-themes-plugins-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-themes-plugins-detail wpvivid-close" style="<?php esc_attr_e($themes_plugins_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary wpvivid-custom-themes-plugins-info">
                    <div class="spinner" style="margin: 0 5px 10px 0; float: left;"></div>
                    <div style="float: left;">Archieving themes and plugins</div>
                    <div style="clear: both;"></div>
                </td>
            </tr>
            <!-------- uploads -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-uploads-check" <?php esc_attr_e($uploads_check.$checkbox_disable); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-uploads-detail">wp-content/uploads</td>
                <td class="column-description desc wpvivid-handle-uploads-detail uploads-desc"><?php _e($uploads_descript); ?></td>
                <th class="wpvivid-handle-uploads-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-uploads-detail wpvivid-close" style="<?php esc_attr_e($uploads_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary">
                    <table class="wp-list-table widefat plugins" style="width:100%;">
                        <thead>
                        <tr>
                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                <label class="wpvivid-refresh-tree wpvivid-refresh-uploads-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder Tree</label>
                            </th>
                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                <div class="wpvivid-custom-uploads-tree">
                                    <div class="wpvivid-custom-tree wpvivid-custom-uploads-tree-info"></div>
                                </div>
                            </td>
                            <td class="wpvivid-custom-uploads-right">
                                <div class="wpvivid-custom-uploads-table wpvivid-custom-exclude-uploads-list">
                                    <?php
                                    if(!$this->is_staging_site){
                                        echo $this->wpvivid_load_custom_upload();
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <div>
                                    <div style="float: left; margin-right: 10px;">
                                        <input class="button-primary wpvivid-exclude-uploads-folder-btn" type="submit" value="Exclude Folders" disabled />
                                    </div>
                                    <small>
                                        <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                            <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                        </div>
                                    </small>
                                    <div style="clear: both;"></div>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                        <div style="clear:both;"></div>
                    </table>
                    <div style="margin-top: 10px;">
                        <div style="float: left; margin-right: 10px;">
                            <input type="text" class="regular-text wpvivid-uploads-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="<?php esc_attr_e($upload_extension); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,'')"/>
                            <input type="button" class="wpvivid-uploads-extension-rule-btn" value="Save" />
                        </div>
                        <small>
                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                            </div>
                        </small>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            <!-------- content -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-content-check" <?php esc_attr_e($content_check.$checkbox_disable); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-content-detail">wp-content</td>
                <td class="column-description desc wpvivid-handle-content-detail content-desc"><?php _e($contents_descript); ?></td>
                <th class="wpvivid-handle-content-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-content-detail wpvivid-close" style="<?php esc_attr_e($content_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary">
                    <table class="wp-list-table widefat plugins" style="width:100%;">
                        <thead>
                        <tr>
                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                <label class="wpvivid-refresh-tree wpvivid-refresh-content-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder Tree</label>
                            </th>
                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                <div class="wpvivid-custom-uploads-tree">
                                    <div class="wpvivid-custom-tree wpvivid-custom-content-tree-info"></div>
                                </div>
                            </td>
                            <td class="wpvivid-custom-uploads-right">
                                <div class="wpvivid-custom-uploads-table wpvivid-custom-exclude-content-list">
                                    <?php
                                    if(!$this->is_staging_site){
                                        echo $this->wpvivid_load_custom_content();
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <div style="float: left; margin-right: 10px;">
                                    <input class="button-primary wpvivid-exclude-content-folder-btn" type="submit" value="Exclude Folders" disabled />
                                </div>
                                <small>
                                    <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                        <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                    </div>
                                </small>
                                <div style="clear: both;"></div>
                            </td>
                        </tr>
                        </tfoot>
                        <div style="clear:both;"></div>
                    </table>
                    <div style="margin-top: 10px;">
                        <div style="float: left; margin-right: 10px;">
                            <input type="text" class="regular-text wpvivid-content-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="<?php esc_attr_e($content_extension); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,'')"/>
                            <input type="button" class="wpvivid-content-extension-rule-btn" value="Save" />
                        </div>
                        <small>
                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                            </div>
                        </small>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            <!-------- additional files -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-additional-file-check" <?php esc_attr_e($additional_file_check); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-additional-file-detail">Additional Files/Folder</td>
                <td class="column-description desc wpvivid-handle-additional-file-detail additional-file-desc"><?php _e($additional_file_descript); ?></td>
                <th class="wpvivid-handle-additional-file-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-additional-file-detail wpvivid-close" style="<?php esc_attr_e($additional_file_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary">
                    <table class="wp-list-table widefat plugins" style="width:100%;">
                        <thead>
                        <tr>
                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                <label class="wpvivid-refresh-tree wpvivid-refresh-additional-file-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder/File Tree</label>
                            </th>
                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                <div class="wpvivid-custom-uploads-tree">
                                    <div class="wpvivid-custom-tree wpvivid-custom-additional-file-tree-info"></div>
                                </div>
                            </td>
                            <td class="wpvivid-custom-uploads-right">
                                <div class="wpvivid-custom-uploads-table wpvivid-custom-include-additional-file-list">
                                    <?php
                                    if(!$this->is_staging_site){
                                        echo $this->wpvivid_load_additional_file();
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <div style="float: left; margin-right: 10px;">
                                    <input class="button-primary wpvivid-include-additional-file-btn" type="submit" value="Include folders/files" disabled />
                                </div>
                                <small>
                                    <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                        <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                    </div>
                                </small>
                                <div style="clear: both;"></div>
                            </td>
                        </tr>
                        </tfoot>
                        <div style="clear:both;"></div>
                    </table>
                    <div style="margin-top: 10px;">
                        <div style="float: left; margin-right: 10px;">
                            <input type="text" class="regular-text wpvivid-additional-file-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="<?php esc_attr_e($additional_file_extension); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,'')"/>
                            <input type="button" class="wpvivid-additional-file-extension-rule-btn" value="Save" />
                        </div>
                        <small>
                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                            </div>
                        </small>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function wpvivid_load_custom_upload(){
        $options = get_option('wpvivid_staging_history', array());
        $ret = '';
        if(isset($options['uploads_list']) && !empty($options['uploads_list'])) {
            foreach ($options['uploads_list'] as $index => $value) {
                $ret .= '<ul style=\'margin: 0;\'>
                            <li>
                                <div class="'.$value['type'].'"></div>
                                <div class="wpvivid-custom-li-font">'.$value['name'].'</div>
                                <div class="wpvivid-custom-li-close" onclick="wpvivid_remove_custom_tree(this);" title="Remove" style="cursor: pointer;">X</div>
                            </li>
                         </ul>';
            }
        }
        return $ret;
    }

    public function wpvivid_load_custom_content(){
        $options = get_option('wpvivid_staging_history', array());
        $ret = '';
        if(isset($options['content_list']) && !empty($options['content_list'])) {
            foreach ($options['content_list'] as $index => $value) {
                $ret .= '<ul style=\'margin: 0;\'>
                            <li>
                                <div class="'.$value['type'].'"></div>
                                <div class="wpvivid-custom-li-font">'.$value['name'].'</div>
                                <div class="wpvivid-custom-li-close" onclick="wpvivid_remove_custom_tree(this);" title="Remove" style="cursor: pointer;">X</div>
                            </li>
                         </ul>';
            }
        }
        return $ret;
    }

    public function wpvivid_load_additional_file(){
        $options = get_option('wpvivid_staging_history', array());
        $ret = '';
        if(isset($options['additional_file_list']) && !empty($options['additional_file_list'])) {
            foreach ($options['additional_file_list'] as $index => $value) {
                $ret .= '<ul style=\'margin: 0;\'>
                            <li>
                                <div class="'.$value['type'].'"></div>
                                <div class="wpvivid-custom-li-font">'.$value['name'].'</div>
                                <div class="wpvivid-custom-li-close" onclick="wpvivid_remove_custom_tree(this);" title="Remove" style="cursor: pointer;">X</div>
                            </li>
                         </ul>';
            }
        }
        return $ret;
    }

    public function load_js(){
        $upload_dir = wp_upload_dir();
        $upload_path = $this->is_staging_site === false ?  $upload_dir['basedir'] : $this->staging_home_path.'/wp-content/uploads';
        $upload_path = str_replace('\\','/',$upload_path);
        $upload_path = $upload_path.'/';
        $this->custom_uploads_path = $upload_path;

        $content_dir = $this->is_staging_site === false ? WP_CONTENT_DIR : $this->staging_home_path.'/wp-content';
        $content_path = str_replace('\\','/',$content_dir);
        $content_path = $content_path.'/';
        $this->custom_content_path = $content_path;

        $additional_file_path = $this->is_staging_site === false ? str_replace('\\','/',get_home_path()) : str_replace('\\','/',$this->staging_home_path);
        $this->custom_additional_file_path = $additional_file_path;
        ?>
        <script>
            function wpvivid_handle_custom_open_close(obj, sub_obj){
                if(obj.hasClass('wpvivid-close')) {
                    sub_obj.hide();
                    sub_obj.prev().find('details').prop('open', false);
                    sub_obj.removeClass('wpvivid-open');
                    sub_obj.addClass('wpvivid-close');
                    sub_obj.prev().css('background-color', '#fff');
                    obj.prev().css('background-color', '#f1f1f1');
                    obj.prev().find('details').prop('open', true);
                    obj.show();
                    obj.removeClass('wpvivid-close');
                    obj.addClass('wpvivid-open');
                }
                else{
                    obj.hide();
                    obj.prev().css('background-color', '#fff');
                    obj.prev().find('details').prop('open', false);
                    obj.removeClass('wpvivid-open');
                    obj.addClass('wpvivid-close');
                }
            }

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-database-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-themes-plugins-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-themes-plugins-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-uploads-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-uploads-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-content-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-content-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-additional-file-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-additional-file-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-custom-check', function() {
                if (jQuery(this).prop('checked')) {
                    if(!jQuery(this).hasClass('wpvivid-custom-core-check')) {
                        jQuery(jQuery(this).parents('tr').next().get(0)).css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-check').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status) {
                        if (!jQuery(this).hasClass('wpvivid-custom-core-check')) {
                            jQuery(jQuery(this).parents('tr').next().get(0)).css({'pointer-events': 'none', 'opacity': '0.4'});
                        }
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one item under Custom option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-database-table-check', function() {
                if(jQuery(this).prop('checked')){
                    if(jQuery(this).hasClass('wpvivid-database-base-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').prop('checked', true);
                    }
                    else if(jQuery(this).hasClass('wpvivid-database-woo-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').prop('checked', true);
                    }
                    else if(jQuery(this).hasClass('wpvivid-database-other-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    if (jQuery(this).hasClass('wpvivid-database-base-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                    else if (jQuery(this).hasClass('wpvivid-database-woo-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                    else if (jQuery(this).hasClass('wpvivid-database-other-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=base_db][name=Database]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-base-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[name=Database]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-base-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one table type under the Database option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=woo_db][name=Database]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-woo-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[name=Database]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-woo-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one table type under the Database option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=other_db][name=Database]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-other-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[name=Database]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-other-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one table type under the Database option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-themes-plugins-table-check', function(){
                if(jQuery(this).prop('checked')){
                    if(jQuery(this).hasClass('wpvivid-themes-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').prop('checked', true);
                    }
                    else if(jQuery(this).hasClass('wpvivid-plugins-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    if (jQuery(this).hasClass('wpvivid-themes-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                        }
                    }
                    else if (jQuery(this).hasClass('wpvivid-plugins-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                                if(jQuery(this).val() !== 'wpvivid-backuprestore' && jQuery(this).val() !== 'wpvivid-backup-pro'){
                                    jQuery(this).prop('checked', false);
                                }
                            });
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                        }
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=themes][name=Themes]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-themes-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(!check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                    }
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-themes-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=plugins][name=Plugins]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-plugins-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(!check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                    }
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-plugins-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-uploads-extension-rule-btn', function(){
                var value = jQuery(this).prev().val();
                if(value!=='') {
                    wpvivid_update_staging_exclude_extension('upload', value);
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-content-extension-rule-btn', function(){
                var value = jQuery(this).prev().val();
                if(value!=='') {
                    wpvivid_update_staging_exclude_extension('content', value);
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-additional-file-extension-rule-btn', function(){
                var value = jQuery(this).prev().val();
                if(value!=='') {
                    wpvivid_update_staging_exclude_extension('additional_file', value);
                }
            });

            function wpvivid_update_staging_exclude_extension(type, value){
                var ajax_data = {
                    'action': 'wpvividstg_update_staging_exclude_extension',
                    'type': type,
                    'exclude_content': value
                };
                jQuery(this).css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function (data) {
                    jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success') {
                        }
                    }
                    catch (err) {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = wpvivid_output_ajaxerror('saving staging extension', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-custom-li-close', function(){
                jQuery(this).parent().parent().remove();
            });
        </script>
        <?php
    }
}

class WPvivid_Staging
{
    public $main_tab;
    public $end_shutdown_function;
    public $screen_ids;
    public $plugin_name;
    public $version;

    public $log;
    public $log_page;
    public $pro_page;
    public $new_wp_page;

    public function __construct()
    {
        $this->version = WPVIVID_STAGING_VERSION;
        $this->plugin_name = WPVIVID_STAGING_SLUG;

        include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-staging-copy-db-ex.php';
        include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-staging-copy-files-ex.php';
        include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-staging-task-ex.php';
        include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-staging-log.php';
        include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-staging-log-page.php';
        include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-staging-pro-page.php';
        include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-staging-crypt.php';
        include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-staging-create-new-wp.php';

        $this->log=new WPvivid_Staging_Log();
        $this->log_page=new WPvivid_Staging_Log_Page();
        $this->pro_page=new WPvivid_Staging_pro_page();
        $this->new_wp_page=new WPvivid_Staging_Create_New_WP();
        add_filter('wpvividstg_get_admin_url',array($this,'get_admin_url'),10);
        add_filter('wpvivid_add_staging_side_bar', array($this, 'wpvivid_add_staging_side_bar'), 11, 2);

        if(is_admin())
        {
            $this->screen_ids=array();
            if(is_multisite())
            {
                //
                $this->screen_ids[]='toplevel_page_'.$this->plugin_name.'-network';
                $this->screen_ids[]='wpvivid-staging_page_wpvividstg-log-network';
                $this->screen_ids[]='wpvivid-staging_page_wpvividstg-setting-network';
                $this->screen_ids[]='wpvivid-staging_page_wpvividstg-pro-network';
                $this->screen_ids[]='wpvivid-staging_page_wpvividstg-newwp-network';
            }
            else
            {
                $this->screen_ids[]='toplevel_page_'.$this->plugin_name;
                $this->screen_ids[]='wpvivid-staging_page_wpvividstg-log';
                $this->screen_ids[]='wpvivid-staging_page_wpvividstg-setting';
                $this->screen_ids[]='wpvivid-staging_page_wpvividstg-pro';
                $this->screen_ids[]='wpvivid-staging_page_wpvividstg-newwp';
            }


            add_action('admin_enqueue_scripts',array( $this,'enqueue_styles'));
            add_action('admin_enqueue_scripts',array( $this,'enqueue_scripts'));

            if(is_multisite())
            {
                add_action('network_admin_menu',array( $this,'add_plugin_admin_menu'));
            }
            else
            {
                add_action('admin_menu',array( $this,'add_plugin_admin_menu'));
            }

            $plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . 'wpvivid-staging.php' );
            add_filter('plugin_action_links_' . $plugin_basename, array( $this,'add_action_links'));

            add_filter('wpvivid_export_setting_addon', array($this, 'export_setting_addon'), 11);

            $this->load_ajax();
        }

        add_action( "init",array($this,'staging_site'));
    }

    public function get_admin_url($admin_url)
    {
        if(is_multisite())
        {
            $admin_url = network_admin_url();
        }
        else
        {
            $admin_url =admin_url();
        }

        return $admin_url;
    }

    public function wpvivid_add_staging_side_bar($html, $show_schedule)
    {
        $wpvivid_staging_version=WPVIVID_STAGING_VERSION;
        $html = '<div class="postbox">
                    <h2>
                        <span style="margin-right: 5px;">Current Version: '.$wpvivid_staging_version.'</span>
                        <span style="margin-right: 5px;">|</span>
                        <span><a href="https://wpvivid.com/wpvivid-staging-changelog" style="text-decoration: none;">Changelog</a></span>
                    </h2>
                 </div>
                 <div class="postbox">
                    <h2><span>Troubleshooting</span></h2>
                    <div class="inside">
                        <table class="widefat" cellpadding="0">
                            <tbody>
                            <tr class="alternate">
                                <td class="row-title">Read <a href="https://wpvivid.com/troubleshooting-issues-wpvivid-staging-pro" target="_blank">Troubleshooting Page</a> for faster solutions.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                 </div>
                 <div class="postbox">
                    <h2><span>Documentation</span></h2>
                    <div class="inside">
                        <table class="widefat" cellpadding="0">
                            <tbody>
                                <tr><td class="row-title"><a href="https://wpvivid.com/wpvivid-backup-pro-create-staging-site" target="_blank">Create A Staging Site</a></td></tr>
                                <tr class="alternate"><td class="row-title"><a href="https://wpvivid.com/wpvivid-backup-pro-publish-staging-to-live" target="_blank">Publish A Staging Site to A Live Site</a></td></tr>
                                <tr><td class="row-title"><a href="https://wpvivid.com/wpvivid-staging-pro-create-staging-site-for-wordpress-multisite" target="_blank">Create A Staging for A WordPress MU</a></td></tr>
                            </tbody>
                        </table>
                    </div>
                 </div>';
        return $html;
    }

    public function load_ajax()
    {
        add_action('wp_ajax_wpvividstg_start_staging', array($this, 'start_staging'));
        add_action('wp_ajax_nopriv_wpvividstg_start_staging', array($this, 'start_staging'));
        add_action('wp_ajax_wpvividstg_get_staging_progress', array($this, 'get_staging_progress'));
        add_action('wp_ajax_nopriv_wpvividstg_get_staging_progress', array($this, 'get_staging_progress'));
        add_action('wp_ajax_wpvividstg_delete_site', array($this, 'delete_site'));
        add_action('wp_ajax_wpvividstg_delete_cancel_staging_site', array($this, 'delete_cancel_staging_site'));
        add_action('wp_ajax_wpvividstg_check_staging_dir', array($this, 'check_staging_dir'));
        add_action('wp_ajax_wpvividstg_push_site', array($this, 'push_site'));
        add_action('wp_ajax_wpvividstg_copy_site', array($this, 'copy_site'));
        //
        add_action('wp_ajax_wpvividstg_get_mu_site_info', array($this, 'get_mu_site_info'));

        add_action('wp_ajax_wpvividstg_push_start_staging', array($this, 'push_start_staging'));
        add_action('wp_ajax_wpvividstg_push_restart_staging', array($this, 'push_restart_staging'));
        add_action('wp_ajax_nopriv_wpvividstg_push_restart_staging', array($this, 'push_restart_staging'));

        add_action('wp_ajax_wpvividstg_copy_start_staging', array($this, 'copy_start_staging'));
        add_action('wp_ajax_wpvividstg_copy_restart_staging', array($this, 'copy_restart_staging'));
        add_action('wp_ajax_nopriv_wpvividstg_copy_restart_staging', array($this, 'copy_restart_staging'));

        add_action('wp_ajax_wpvividstg_get_custom_database_tables_info',array($this, 'get_custom_database_tables_info'));
        add_action('wp_ajax_wpvividstg_get_custom_themes_plugins_info', array($this, 'get_custom_themes_plugins_info'));
        add_action('wp_ajax_wpvividstg_get_custom_dir_uploads_info', array($this, 'get_custom_dir_uploads_info'));
        add_action('wp_ajax_wpvividstg_get_custom_dir_additional_info', array($this, 'get_custom_dir_additional_info'));

        add_action('wp_ajax_wpvividstg_cancel_staging', array($this, 'cancel_staging'));
        add_action('wp_ajax_wpvividstg_test_additional_database_connect', array($this, 'test_additional_database_connect'));
        add_action('wp_ajax_wpvividstg_update_staging_exclude_extension', array($this, 'update_staging_exclude_extension'));
        add_action('wp_ajax_wpvividstg_save_setting', array($this, 'save_setting'));

        add_action('wp_ajax_wpvivid_get_mu_list', array($this, 'get_mu_list'));
        //
    }

    public function save_setting()
    {
        $this->ajax_check_security('manage_options');
        $ret=array();
        try
        {
            if(isset($_POST['setting'])&&!empty($_POST['setting']))
            {
                $json_setting = $_POST['setting'];
                $json_setting = stripslashes($json_setting);
                $setting = json_decode($json_setting, true);
                if (is_null($setting))
                {
                    echo 'json decode failed';
                    die();
                }

                $options=get_option('wpvivid_staging_options',array());

                $options['staging_db_insert_count'] = intval($setting['staging_db_insert_count']);
                $options['staging_db_replace_count'] = intval($setting['staging_db_replace_count']);
                $options['staging_file_copy_count'] = intval($setting['staging_file_copy_count']);
                $options['staging_exclude_file_size'] = intval($setting['staging_exclude_file_size']);
                $options['staging_memory_limit'] = $setting['staging_memory_limit'].'M';
                $options['staging_max_execution_time'] = intval($setting['staging_max_execution_time']);
                $options['staging_resume_count'] = intval($setting['staging_resume_count']);
                $options['not_need_login']= intval($setting['not_need_login']);
                $options['staging_overwrite_permalink'] = intval($setting['staging_overwrite_permalink']);

                $options['staging_request_timeout']= intval($setting['staging_request_timeout']);
                update_option('wpvivid_staging_options',$options);

                $ret['result']='success';
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        echo json_encode($ret);
        die();
    }

    public function ajax_check_security($role='administrator')
    {
        check_ajax_referer( 'wpvivid_ajax', 'nonce' );
        $check=is_admin()&&current_user_can($role);
        $check=apply_filters('wpvivid_ajax_check_security',$check);
        if(!$check)
        {
            die();
        }
    }

    public function enqueue_styles()
    {
        if(in_array(get_current_screen()->id,$this->screen_ids))
        {
            wp_enqueue_style($this->plugin_name.'jstree', WPVIVID_STAGING_PLUGIN_URL . 'includes/js/jstree/dist/themes/default/style.min.css', array(), $this->version, 'all');
            wp_enqueue_style($this->plugin_name, WPVIVID_STAGING_PLUGIN_URL . 'includes/css/wpvivid-staging-custom.css', array(), $this->version, 'all');
        }
    }

    public function enqueue_scripts()
    {
        if(in_array(get_current_screen()->id,$this->screen_ids))
        {
            wp_enqueue_script($this->plugin_name, WPVIVID_STAGING_PLUGIN_URL . 'includes/js/wpvivid-staging-admin.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name.'jstree', WPVIVID_STAGING_PLUGIN_URL . 'includes/js/jstree/dist/jstree.min.js', array('jquery'), $this->version, false);
            wp_localize_script($this->plugin_name, 'wpvivid_ajax_object', array('ajax_url' => admin_url('admin-ajax.php'),'ajax_nonce'=>wp_create_nonce('wpvivid_ajax')));

            wp_enqueue_script('plupload-all');
        }
    }

    public function add_plugin_admin_menu()
    {
        $menu['page_title']=__('WPvivid Staging');
        $menu['menu_title']=__('WPvivid Staging');
        $menu['capability']='administrator';
        $menu['menu_slug']= $this->plugin_name;
        $menu['function']=array($this, 'display_plugin_setup_page');
        $menu['icon_url']='dashicons-cloud';
        $menu['position']=100;


        add_menu_page( $menu['page_title'],$menu['menu_title'], $menu['capability'], $menu['menu_slug'], $menu['function'], $menu['icon_url'], $menu['position']);

        /*
        $submenu['parent_slug']=$this->plugin_name;
        $submenu['page_title']=__('WPvivid Create New WP');
        $submenu['menu_title']='Create New WP';
        $submenu['capability']='administrator';
        $submenu['menu_slug']='wpvividstg-newwp';
        $submenu['index']=2;
        $submenu['function']=array($this->new_wp_page, 'output_page');

        add_submenu_page(
            $submenu['parent_slug'],
            $submenu['page_title'],
            $submenu['menu_title'],
            $submenu['capability'],
            $submenu['menu_slug'],
            $submenu['function']);
        */

        $submenu['parent_slug']=$this->plugin_name;
        $submenu['page_title']=__('WPvivid Staging');
        $submenu['menu_title']='Settings';
        $submenu['capability']='administrator';
        $submenu['menu_slug']='wpvividstg-setting';
        $submenu['index']=3;
        $submenu['function']=array($this, 'init_setting_page');

        add_submenu_page(
            $submenu['parent_slug'],
            $submenu['page_title'],
            $submenu['menu_title'],
            $submenu['capability'],
            $submenu['menu_slug'],
            $submenu['function']);

        $submenu['parent_slug']=$this->plugin_name;
        $submenu['page_title']=__('WPvivid Log');
        $submenu['menu_title']='Logs';
        $submenu['capability']='administrator';
        $submenu['menu_slug']='wpvividstg-log';
        $submenu['index']=4;
        $submenu['function']=array($this->log_page, 'init_page');

        add_submenu_page(
            $submenu['parent_slug'],
            $submenu['page_title'],
            $submenu['menu_title'],
            $submenu['capability'],
            $submenu['menu_slug'],
            $submenu['function']);

        $submenu['parent_slug']=$this->plugin_name;
        $submenu['page_title']=__('WPvivid Staging');
        $submenu['menu_title']='License';
        $submenu['capability']='administrator';
        $submenu['menu_slug']='wpvividstg-pro';
        $submenu['index']=5;
        $submenu['function']=array($this->pro_page, 'display_plugin_pro_page');

        add_submenu_page(
            $submenu['parent_slug'],
            $submenu['page_title'],
            $submenu['menu_title'],
            $submenu['capability'],
            $submenu['menu_slug'],
            $submenu['function']);


    }

    public function display_plugin_setup_page()
    {
        ?>
        <div class="wrap" style="max-width:1720px;">
            <?php
            $this->init_page();
            ?>
        </div>
        <?php
    }

    public function add_action_links( $links )
    {
        $settings_link = array(
            '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
        );
        return array_merge(  $settings_link, $links );
    }

    public function init_page()
    {
        ?>
        <div class="wrap" style="max-width:1720px;">
            <h1>
                <?php
                $plugin_display_name = 'WPvivid Staging';
                _e($plugin_display_name);

                $options=get_option('wpvivid_staging_options',array());
                if(isset( $options['staging_request_timeout']))
                {
                    $request_timeout=$options['staging_request_timeout'];
                }
                else
                {
                    $request_timeout=WPVIVID_STAGING_REQUEST_TIMEOUT;
                }
                ?>
            </h1>
            <div id="wpvivid_staging_notice"></div>
            <script>
                function wpvivid_include_exclude_folder(type, parent_id, tree_path)
                {
                    var select_folders = '';
                    if (type === 'uploads')
                    {
                        select_folders = jQuery('#' + parent_id).find('.wpvivid-custom-uploads-tree-info').jstree(true).get_selected(true);
                        var list_obj = jQuery('#' + parent_id).find('.wpvivid-custom-exclude-uploads-list');
                    }
                    if (type === 'content')
                    {
                        select_folders = jQuery('#' + parent_id).find('.wpvivid-custom-content-tree-info').jstree(true).get_selected(true);
                        var list_obj = jQuery('#' + parent_id).find('.wpvivid-custom-exclude-content-list');
                    }
                    if (type === 'additional_file')
                    {
                        select_folders = jQuery('#' + parent_id).find('.wpvivid-custom-additional-file-tree-info').jstree(true).get_selected(true);
                        var list_obj = jQuery('#' + parent_id).find('.wpvivid-custom-include-additional-file-list');
                    }
                    jQuery.each(select_folders, function (index, select_item)
                    {
                        if (select_item.id !== tree_path)
                        {
                            var value = select_item.id;
                            value = value.replace(tree_path, '');
                            if (!wpvivid_check_custom_tree_repeat(type, value, parent_id))
                            {
                                var class_name = select_item.icon === 'jstree-folder' ? 'wpvivid-custom-li-folder-icon' : 'wpvivid-custom-li-file-icon';
                                var tr = "<ul style='margin: 0;'>" +
                                    "<li>" +
                                    "<div class='" + class_name + "'></div>" +
                                    "<div class='wpvivid-custom-li-font'>" + value + "</div>" +
                                    "<div class='wpvivid-custom-li-close' onclick='wpvivid_remove_custom_tree(this);' title='Remove' style='cursor: pointer;'>X</div>" +
                                    "</li>" +
                                    "</ul>";
                                list_obj.append(tr);
                            }
                        }
                    });
                }

                function wpvivid_check_custom_tree_repeat(type, value, parent_id)
                {
                    var brepeat = false;
                    var list_class = 'wpvivid-custom-exclude-uploads-list';
                    if (type === 'uploads')
                    {
                        list_class = 'wpvivid-custom-exclude-uploads-list';
                    }
                    if (type === 'content')
                    {
                        list_class = 'wpvivid-custom-exclude-content-list';
                    }
                    if (type === 'additional_file')
                    {
                        list_class = 'wpvivid-custom-include-additional-file-list';
                    }
                    jQuery('#' + parent_id).find('.' + list_class + ' ul').find('li div:eq(1)').each(function ()
                    {
                        if (value === this.innerHTML)
                        {
                            brepeat = true;
                        }
                    });
                    return brepeat;
                }

                function wpvivid_remove_custom_tree(obj)
                {
                    jQuery(obj).parent().parent().remove();
                }

                var staging_requet_timeout=<?php echo $request_timeout ?>;

                var archieve_info = {};
                archieve_info.src_db_retry    = 0;
                archieve_info.src_theme_retry = 0;
                archieve_info.des_db_retry    = 0;
                archieve_info.des_theme_retry = 0;

                function wpvivid_refresh_staging_database(parent_id, is_staging, staging_site_id)
                {
                    if(is_staging == '1')
                    {
                        archieve_info.des_db_retry = 0;
                    }
                    else
                    {
                        archieve_info.src_db_retry = 0;
                    }
                    var custom_database_loading = '<div class="spinner" style="margin: 0 5px 10px 0; float: left;"></div>' +
                        '<div style="float: left;">Archieving database tables</div>' +
                        '<div style="clear: both;"></div>';
                    jQuery('#' + parent_id).find('.wpvivid-custom-database-info').html('');
                    jQuery('#' + parent_id).find('.wpvivid-custom-database-info').html(custom_database_loading);
                    wpvivid_get_custom_database_tables_info(parent_id, is_staging, staging_site_id);
                }

                function wpvivid_get_custom_database_tables_info(parent_id, is_staging, staging_site_id)
                {
                    var id = staging_site_id;

                    var ajax_data = {
                        'action': 'wpvividstg_get_custom_database_tables_info',
                        'id': id,
                        'is_staging': is_staging
                    };
                    wpvivid_post_request(ajax_data, function (data)
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#' + parent_id).find('.wpvivid-custom-database-info').html('');
                            jQuery('#' + parent_id).find('.wpvivid-custom-database-info').html(jsonarray.html);
                        }
                    }, function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        var need_retry_custom_database = false;
                        var retry_times=0;
                        if(is_staging == '1')
                        {
                            archieve_info.des_db_retry++;
                            retry_times = archieve_info.des_db_retry;
                        }
                        else{
                            archieve_info.src_db_retry++;
                            retry_times = archieve_info.src_db_retry;
                        }
                        if(retry_times < 10){
                            need_retry_custom_database = true;
                        }
                        if(need_retry_custom_database)
                        {
                            setTimeout(function()
                            {
                                wpvivid_get_custom_database_tables_info(parent_id, is_staging, staging_site_id);
                            }, 3000);
                        }
                        else{
                            var refresh_btn = '<input type="submit" class="button-primary" value="Refresh" onclick="wpvivid_refresh_staging_database(\''+parent_id+'\', \''+is_staging+'\', \''+staging_site_id+'\');">';
                            jQuery('#' + parent_id).find('.wpvivid-custom-database-info').html('');
                            jQuery('#' + parent_id).find('.wpvivid-custom-database-info').html(refresh_btn);
                        }
                    });
                }

                function wpvivid_refresh_staging_themes_plugins(parent_id, is_staging, staging_site_id, home_path)
                {
                    if(is_staging == '1')
                    {
                        archieve_info.des_theme_retry = 0;
                    }
                    else{
                        archieve_info.src_theme_retry = 0;
                    }
                    var custom_themes_plugins_loading = '<div class="spinner" style="margin: 0 5px 10px 0; float: left;"></div>' +
                        '<div style="float: left;">Archieving themes and plugins</div>' +
                        '<div style="clear: both;"></div>';
                    jQuery('#' + parent_id).find('.wpvivid-custom-themes-plugins-info').html('');
                    jQuery('#' + parent_id).find('.wpvivid-custom-themes-plugins-info').html(custom_themes_plugins_loading);
                    wpvivid_get_custom_themes_plugins_info(parent_id, is_staging, staging_site_id, home_path);
                }

                function wpvivid_get_custom_themes_plugins_info(parent_id, is_staging, staging_site_id, home_path)
                {
                    var id = parent_id;
                    if(is_staging == '1')
                    {
                        id = staging_site_id;
                    }
                    var ajax_data = {
                        'action': 'wpvividstg_get_custom_themes_plugins_info',
                        'id': id,
                        'is_staging': is_staging,
                        'staging_path': home_path
                    };
                    wpvivid_post_request(ajax_data, function (data)
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#' + parent_id).find('.wpvivid-custom-themes-plugins-info').html('');
                            jQuery('#' + parent_id).find('.wpvivid-custom-themes-plugins-info').html(jsonarray.html);
                        }
                    }, function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        var need_retry_custom_themes = false;
                        if(is_staging == '1')
                        {
                            archieve_info.des_theme_retry++;
                            var retry_times = archieve_info.des_theme_retry;
                        }
                        else{
                            archieve_info.src_theme_retry++;
                            var retry_times = archieve_info.src_theme_retry;
                        }
                        if(retry_times < 10){
                            need_retry_custom_themes = true;
                        }
                        if(need_retry_custom_themes) {
                            setTimeout(function(){
                                wpvivid_get_custom_themes_plugins_info(parent_id, is_staging, staging_site_id, home_path);
                            }, 3000);
                        }
                        else{
                            var refresh_btn = '<input type="submit" class="button-primary" value="Refresh" onclick="wpvivid_refresh_staging_themes_plugins(\''+parent_id+'\', \''+is_staging+'\', \''+staging_site_id+'\', \''+home_path+'\');">';
                            jQuery('#' + parent_id).find('.wpvivid-custom-themes-plugins-info').html('');
                            jQuery('#' + parent_id).find('.wpvivid-custom-themes-plugins-info').html(refresh_btn);
                        }
                    });
                }

                function wpvivid_archieve_uploads_custom_tree(is_staging, path, parent_id, refresh)
                {
                    if (refresh) {
                        jQuery('#' + parent_id).find('.wpvivid-custom-uploads-tree-info').jstree("refresh");
                    }
                    else {
                        jQuery('#' + parent_id).find('.wpvivid-custom-uploads-tree-info').on('activate_node.jstree', function (event, data) {
                        }).jstree({
                            "core": {
                                "check_callback": true,
                                "multiple": true,
                                "data": function (node_id, callback) {
                                    var tree_node = {
                                        'node': node_id,
                                        'path': path
                                    };
                                    var ajax_data = {
                                        'action': 'wpvividstg_get_custom_dir_uploads_info',
                                        'tree_node': tree_node,
                                        'is_staging': is_staging
                                    };
                                    ajax_data.nonce=wpvivid_ajax_object.ajax_nonce;
                                    jQuery.ajax({
                                        type: "post",
                                        url: wpvivid_ajax_object.ajax_url,
                                        data: ajax_data,
                                        success: function (data) {
                                            var jsonarray = jQuery.parseJSON(data);
                                            callback.call(this, jsonarray.nodes);
                                            jQuery('#' + parent_id).find('.wpvivid-exclude-uploads-folder-btn').attr('disabled', false);
                                        },
                                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                                            alert("error");
                                        },
                                        timeout: 30000
                                    });
                                },
                                'themes': {
                                    'stripes': true
                                }
                            }
                        });
                    }
                }

                function wpvivid_archieve_content_custom_tree(is_staging, path, parent_id, refresh)
                {
                    if (refresh) {
                        jQuery('#' + parent_id).find('.wpvivid-custom-content-tree-info').jstree("refresh");
                    }
                    else {
                        jQuery('#' + parent_id).find('.wpvivid-custom-content-tree-info').on('activate_node.jstree', function (event, data) {
                        }).jstree({
                            "core": {
                                "check_callback": true,
                                "multiple": true,
                                "data": function (node_id, callback) {
                                    var tree_node = {
                                        'node': node_id,
                                        'path': path
                                    };
                                    var ajax_data = {
                                        'action': 'wpvividstg_get_custom_dir_uploads_info',
                                        'tree_node': tree_node,
                                        'is_staging': is_staging
                                    };
                                    ajax_data.nonce=wpvivid_ajax_object.ajax_nonce;
                                    jQuery.ajax({
                                        type: "post",
                                        url: wpvivid_ajax_object.ajax_url,
                                        data: ajax_data,
                                        success: function (data) {
                                            var jsonarray = jQuery.parseJSON(data);
                                            callback.call(this, jsonarray.nodes);
                                            jQuery('#' + parent_id).find('.wpvivid-exclude-content-folder-btn').attr('disabled', false);
                                        },
                                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                                            alert("error");
                                        },
                                        timeout: 30000
                                    });
                                },
                                'themes': {
                                    'stripes': true
                                }
                            }
                        });
                    }
                }

                function wpvivid_archieve_additional_file_custom_tree(is_staging, path, parent_id, refresh)
                {
                    if (refresh) {
                        jQuery('#' + parent_id).find('.wpvivid-custom-additional-file-tree-info').jstree("refresh");
                    }
                    else {
                        jQuery('#' + parent_id).find('.wpvivid-custom-additional-file-tree-info').on('activate_node.jstree', function (e, data) {
                        }).jstree({
                            "core": {
                                "check_callback": true,
                                "multiple": true,
                                "data": function (node_id, callback) {
                                    var tree_node = {
                                        'node': node_id,
                                        'path': path
                                    };
                                    var ajax_data = {
                                        'action': 'wpvividstg_get_custom_dir_additional_info',
                                        'tree_node': tree_node,
                                        'is_staging': is_staging
                                    };
                                    ajax_data.nonce=wpvivid_ajax_object.ajax_nonce;
                                    jQuery.ajax({
                                        type: "post",
                                        url: wpvivid_ajax_object.ajax_url,
                                        data: ajax_data,
                                        success: function (data) {
                                            var jsonarray = jQuery.parseJSON(data);
                                            callback.call(this, jsonarray.nodes);
                                            jQuery('#' + parent_id).find('.wpvivid-include-additional-file-btn').attr('disabled', false);
                                        },
                                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                                            alert("error");
                                        },
                                        timeout: 30000
                                    });
                                },
                                'themes': {
                                    'stripes': true
                                }
                            }
                        });
                    }
                }

                function wpvivid_init_custom_tree(type, parent_id, is_staging, tree_path, refresh = 0)
                {
                    if (type === 'uploads') {
                        wpvivid_archieve_uploads_custom_tree(is_staging, tree_path, parent_id, refresh);
                    }
                    if (type === 'content') {
                        wpvivid_archieve_content_custom_tree(is_staging, tree_path, parent_id, refresh);
                    }
                    if (type === 'additional_file') {
                        wpvivid_archieve_additional_file_custom_tree(is_staging, tree_path, parent_id, refresh);
                    }
                }

                function load_js(parent_id, is_staging, upload_path, content_path, home_path, staging_site_id = '')
                {
                    jQuery('#' + parent_id).on("click", '.wpvivid-handle-uploads-detail', function () {
                        wpvivid_init_custom_tree('uploads', parent_id, is_staging, upload_path, 0);
                    });

                    jQuery('#' + parent_id).on("click", '.wpvivid-handle-content-detail', function () {
                        wpvivid_init_custom_tree('content', parent_id, is_staging, content_path, 0);
                    });

                    jQuery('#' + parent_id).on("click", '.wpvivid-handle-additional-file-detail', function () {
                        wpvivid_init_custom_tree('additional_file', parent_id, is_staging, home_path, 0);
                    });

                    jQuery('#' + parent_id).on("click", '.wpvivid-refresh-tree', function () {
                        if (jQuery(this).hasClass('wpvivid-refresh-uploads-tree')) {
                            wpvivid_init_custom_tree('uploads', parent_id, is_staging, upload_path, 1);
                        }
                        else if (jQuery(this).hasClass('wpvivid-refresh-content-tree')) {
                            wpvivid_init_custom_tree('content', parent_id, is_staging, content_path, 1);
                        }
                        else if (jQuery(this).hasClass('wpvivid-refresh-additional-file-tree')) {
                            wpvivid_init_custom_tree('additional_file', parent_id, is_staging, home_path, 1);
                        }
                    });

                    jQuery('#' + parent_id).on("click", '.wpvivid-exclude-uploads-folder-btn', function () {
                        wpvivid_include_exclude_folder('uploads', parent_id, upload_path);
                    });

                    jQuery('#' + parent_id).on("click", '.wpvivid-exclude-content-folder-btn', function () {
                        wpvivid_include_exclude_folder('content', parent_id, content_path);
                    });

                    jQuery('#' + parent_id).on("click", '.wpvivid-include-additional-file-btn', function () {
                        wpvivid_include_exclude_folder('additional_file', parent_id, home_path);
                    });

                    jQuery('#' + parent_id).on("click", '.wpvivid-custom-uploads-tree-info', function () {
                        var tree_path = upload_path;
                        var select_path = jQuery(this).jstree(true).get_selected();
                        if (select_path == tree_path) {
                            alert("The root directory is not allowed to select.");
                        }
                    });

                    jQuery('#' + parent_id).on("click", '.wpvivid-custom-content-tree-info', function () {
                        var tree_path = content_path;
                        var select_path = jQuery(this).jstree(true).get_selected();
                        if (select_path == tree_path) {
                            alert("The root directory is not allowed to select.");
                        }
                    });

                    jQuery('#' + parent_id).on("click", '.wpvivid-custom-additional-file-tree-info', function () {
                        var tree_path = home_path;
                        var select_path = jQuery(this).jstree(true).get_selected();
                        if (select_path == tree_path) {
                            alert("The root directory is not allowed to select.");
                        }
                    });


                    if(is_staging){
                        is_staging = '1';
                    }
                    else{
                        is_staging = '0';
                    }
                    wpvivid_get_custom_database_tables_info(parent_id, is_staging, staging_site_id);
                    wpvivid_get_custom_themes_plugins_info(parent_id, is_staging, staging_site_id, home_path);
                }
            </script>
            <?php
            if(!class_exists('WPvivid_Tab_Page_Container_Ex'))
                include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-tab-page-container-ex.php';
            $this->main_tab=new WPvivid_Tab_Page_Container_Ex();
            $data=$this->get_staging_site_data();

            if($data===false)
            {
                $this->main_tab->add_tab('Staging Sites','staging_sites',array($this, 'output_staging_sites_list_page'));
                $this->main_tab->add_tab('Create Staging','create_staging',array($this, 'output_create_staging_site_page'));
                $this->main_tab->add_tab('Create Fresh Install','create_fresh_install',array($this->new_wp_page, 'output_create_wp_page'));
            }
            else
            {
                $this->main_tab->add_tab('Staging Sites','staging_sites',array($this, 'output_staging'));
            }

            $this->main_tab->display();
            ?>
            <script>
                function switch_staging_tab(id)
                {
                    jQuery( document ).trigger( '<?php echo $this->main_tab->container_id ?>-show',id);
                }
            </script>
        </div>
        <?php
    }

    public function init_setting_page()
    {
        ?>
        <div class="wrap" style="max-width:1720px;">
            <h1>
                <?php
                $plugin_display_name = 'WPvivid Staging';
                _e($plugin_display_name);
                ?>
            </h1>
            <?php
            if(!class_exists('WPvivid_Tab_Page_Container_Ex'))
                include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-tab-page-container-ex.php';
            $this->main_tab=new WPvivid_Tab_Page_Container_Ex();

            $this->main_tab->add_tab('Settings','setting',array($this, 'output_setting'));

            $this->main_tab->display();
            ?>
        </div>
        <?php
    }

    public function output_setting()
    {
        ?>
        <div class="postbox quickbackup-addon">
            <div>
                <div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left;">
                    <img src="<?php echo esc_url(WPVIVID_STAGING_PLUGIN_URL.'includes/images/settings.png'); ?>" style="width:50px;height:50px;">
                </div>
                <div class="wpvivid-element-space-bottom">
                    <div class="wpvivid-text-space-bottom" style="margin-bottom: 0;"><?php echo sprintf(__('The settings page of %s Staging plugin.', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid')); ?></div>
                </div>
                <div style="clear: both;"></div>
            </div>
            <?php
            if(!class_exists('WPvivid_Tab_Page_Container_Ex'))
                include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-tab-page-container-ex.php';
            $this->main_tab=new WPvivid_Tab_Page_Container_Ex();

            $tabs=array();
            $tab['title']='Staging Settings';
            $tab['slug']='staging';
            $tab['callback']= array($this, 'output_staging_setting');
            $args['is_parent_tab']=0;
            $args['transparency']=1;
            $tab['args']=$args;
            $tabs[]=$tab;
            foreach ($tabs as $tab)
            {
                $this->main_tab->add_tab($tab['title'], $tab['slug'], $tab['callback'], $tab['args']);
            }

            $this->main_tab->display();
            ?>
            <!--<div><input class="button-primary" id="wpvivid_setting_general_save" type="submit" value="<?php esc_attr_e( 'Save Changes', 'wpvivid' ); ?>" /></div>-->
        </div>
        <script>
            function switch_setting_tab(id)
            {
                jQuery( document ).trigger( '<?php echo $this->main_tab->container_id ?>-show',id);
            }
            jQuery(document).ready(function($)
            {
                <?php
                if(isset($_REQUEST['tabs']))
                {
                ?>
                switch_setting_tab('<?php echo $_REQUEST['tabs'];?>');
                <?php
                }
                ?>
            });
        </script>
        <?php
    }

    public function output_staging_setting()
    {
        ?>
        <div style="margin-top: 10px;">
            <?php
            $this->wpvivid_setting_add_staging_cell_addon();
            ?>
        </div>
        <?php
    }

    public function wpvivid_setting_add_staging_cell_addon()
    {
        $options=get_option('wpvivid_staging_options',array());

        $staging_db_insert_count   = isset($options['staging_db_insert_count']) ? $options['staging_db_insert_count'] : WPVIVID_STAGING_DB_INSERT_COUNT_EX;
        $staging_db_replace_count  = isset($options['staging_db_replace_count']) ? $options['staging_db_replace_count'] : WPVIVID_STAGING_DB_REPLACE_COUNT_EX;
        $staging_file_copy_count   = isset($options['staging_file_copy_count']) ? $options['staging_file_copy_count'] : WPVIVID_STAGING_FILE_COPY_COUNT_EX;
        $staging_exclude_file_size = isset($options['staging_exclude_file_size']) ? $options['staging_exclude_file_size'] : WPVIVID_STAGING_MAX_FILE_SIZE_EX;
        $staging_memory_limit      = isset($options['staging_memory_limit']) ? $options['staging_memory_limit'] : WPVIVID_STAGING_MEMORY_LIMIT_EX;
        $staging_memory_limit      = str_replace('M', '', $staging_memory_limit);
        $staging_max_execution_time= isset($options['staging_max_execution_time']) ? $options['staging_max_execution_time'] : WPVIVID_STAGING_MAX_EXECUTION_TIME_EX;
        $staging_resume_count      = isset($options['staging_resume_count']) ? $options['staging_resume_count'] : WPVIVID_STAGING_RESUME_COUNT_EX;
        $staging_request_timeout      = isset($options['staging_request_timeout']) ? $options['staging_request_timeout'] : WPVIVID_STAGING_REQUEST_TIMEOUT;

        $staging_not_need_login=isset($options['not_need_login']) ? $options['not_need_login'] : true;
        if($staging_not_need_login)
        {
            $staging_not_need_login_check='checked';
        }
        else
        {
            $staging_not_need_login_check='';
        }
        $staging_overwrite_permalink = isset($options['staging_overwrite_permalink']) ? $options['staging_overwrite_permalink'] : true;
        if($staging_overwrite_permalink){
            $staging_overwrite_permalink_check = 'checked';
        }
        else{
            $staging_overwrite_permalink_check = '';
        }
        ?>
        <div class="postbox schedule-tab-block wpvivid-setting-addon" style="margin-bottom: 10px; padding-bottom: 0;">
            <div class="wpvivid-element-space-bottom"><strong><?php _e('DB Copy Count', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_db_insert_count" value="<?php esc_attr_e($staging_db_insert_count); ?>"
                       placeholder="10000" onkeyup="value=value.replace(/\D/g,'')" />
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'Number of DB rows, that are copied within one ajax query. The higher value makes the database copy process faster. 
                Please try a high value to find out the highest possible value. If you encounter timeout errors, try lower values until no 
                more errors occur.', 'wpvivid' ); ?>
            </div>

            <div class="wpvivid-element-space-bottom"><strong><?php _e('DB Replace Count', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_db_replace_count" value="<?php esc_attr_e($staging_db_replace_count); ?>"
                       placeholder="5000" onkeyup="value=value.replace(/\D/g,'')" />
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'Number of DB rows, that are processed within one ajax query. The higher value makes the DB replacement process faster. 
                If timeout erros occur, decrease the value because this process consumes a lot of memory.', 'wpvivid' ); ?>
            </div>

            <div class="wpvivid-element-space-bottom"><strong><?php _e('File Copy Count', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_file_copy_count" value="<?php esc_attr_e($staging_file_copy_count); ?>"
                       placeholder="500" onkeyup="value=value.replace(/\D/g,'')" />
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'Number of files to copy that will be copied within one ajax request. The higher value makes the file file copy process faster. 
                Please try a high value to find out the highest possible value. If you encounter timeout errors, try lower values until no more errors occur.', 'wpvivid' ); ?>
            </div>

            <div class="wpvivid-element-space-bottom"><strong><?php _e('Max File Size', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_exclude_file_size" value="<?php esc_attr_e($staging_exclude_file_size); ?>"
                       placeholder="30" onkeyup="value=value.replace(/\D/g,'')" />MB
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'Maximum size of the files copied to a staging site. All files larger than this value will be ignored. If you set the value of 0 MB, all files will be copied to a staging site.', 'wpvivid' ); ?>
            </div>

            <div class="wpvivid-element-space-bottom"><strong><?php _e('Staging Memory Limit', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_memory_limit" value="<?php esc_attr_e($staging_memory_limit); ?>"
                       placeholder="256" onkeyup="value=value.replace(/\D/g,'')" />MB
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php echo sprintf(__('Adjust this value to apply for a temporary PHP memory limit for %s while creating a staging site. 
                We set this value to 256M by default. Increase the value if you encounter a memory exhausted error. Note: some web hosting 
                providers may not support this.', 'wpvivid'), apply_filters( 'wpvivid_white_label_display', 'WPvivid backup plugin' )); ?>
            </div>

            <div class="wpvivid-element-space-bottom"><strong><?php _e('PHP Script Execution Timeout', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_max_execution_time" value="<?php esc_attr_e($staging_max_execution_time); ?>"
                       placeholder="900" onkeyup="value=value.replace(/\D/g,'')" />
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'The time-out is not your server PHP time-out. With the execution time exhausted, our plugin will shut down the progress of 
                creating a staging site. If the progress  encounters a time-out, that means you have a medium or large sized website. Please try to 
                scale the value bigger.', 'wpvivid' ); ?>
            </div>

            <div class="wpvivid-element-space-bottom"><strong><?php _e('Delay Between Requests', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_request_timeout" value="<?php esc_attr_e($staging_request_timeout); ?>"
                       placeholder="1000" onkeyup="value=value.replace(/\D/g,'')" />ms
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'A lower value will help speed up the process of creating a staging site. However, if your server has a limit on the number of requests, a higher value is recommended.', 'wpvivid' ); ?>
            </div>

            <div class="wpvivid-element-space-bottom">
                <strong>Retrying </strong>
                <select option="setting" name="staging_resume_count">
                    <?php
                    for($resume_count=3; $resume_count<10; $resume_count++){
                        if($resume_count === $staging_resume_count){
                            _e('<option selected="selected" value="'.$resume_count.'">'.$resume_count.'</option>');
                        }
                        else{
                            _e('<option value="'.$resume_count.'">'.$resume_count.'</option>');
                        }
                    }
                    ?>
                </select><strong><?php _e(' times when encountering a time-out error', 'wpvivid'); ?></strong>
            </div>

            <div class="wpvivid-element-space-bottom">
                <label>
                    <input type="checkbox" option="setting" name="not_need_login" <?php esc_attr_e($staging_not_need_login_check); ?> />
                    <span><strong><?php _e('Anyone can visit the staging site', 'wpvivid'); ?></strong></span>
                </label>
            </div>

            <div class="wpvivid-element-space-bottom">
                <span>When the option is checked, anyone will be able to visit the staging site without the need to login. Uncheck it to request a login to visit the staging site.</span>
            </div>

            <div class="wpvivid-element-space-bottom">
                <label>
                    <input type="checkbox" option="setting" name="staging_overwrite_permalink" <?php esc_attr_e($staging_overwrite_permalink_check); ?> />
                    <span><strong><?php _e('Keep permalink when transferring website', 'wpvivid'); ?></strong></span>
                </label>
            </div>

            <div class="wpvivid-element-space-bottom">
                <span>When checked, this option allows you to keep the current permalink structure when you create a staging site or push a staging site to live.</span>
            </div>
        </div>
        <div><input class="button-primary wpvividstg_save_setting" type="submit" value="<?php esc_attr_e( 'Save Changes', 'wpvivid' ); ?>" /></div>
        <script>
            jQuery('.wpvividstg_save_setting').click(function()
            {
                wpvividstg_save_setting();
            });

            function wpvividstg_save_setting()
            {
                var setting_data = wpvivid_ajax_data_transfer('setting');
                var ajax_data = {
                    'action': 'wpvividstg_save_setting',
                    'setting': setting_data,
                };
                jQuery('.wpvividstg_save_setting').css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function (data)
                {
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);

                        jQuery('.wpvividstg_save_setting').css({'pointer-events': 'auto', 'opacity': '1'});
                        if (jsonarray.result === 'success')
                        {
                            location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvividstg-setting'; ?>';
                        }
                        else {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err)
                    {
                        alert(err);
                        jQuery('.wpvividstg_save_setting').css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    jQuery('.wpvividstg_save_setting').css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = wpvivid_output_ajaxerror('changing base settings', textStatus, errorThrown);
                    alert(error_message);
                });
            }
        </script>
        <?php
    }

    public static function wpvivid_check_site_url()
    {
        $site_url = site_url();
        $home_url = home_url();
        $db_site_url = '';
        $db_home_url = '';
        global $wpdb;
        $site_url_sql = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name = %s", 'siteurl' ) );
        $home_url_sql = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name = %s", 'home' ) );
        foreach ( $site_url_sql as $site ){
            $db_site_url = $site->option_value;
        }
        foreach ( $home_url_sql as $home ){
            $db_home_url = $home->option_value;
        }
        if($site_url !== $db_site_url || $home_url !== $db_home_url){
            _e('<div class="notice notice-warning"><p>Warning: An inconsistency was detected between the site url, home url of the database and the actual website url. 
                                        This can cause inappropriate staging site url issues. Please change the site url and home url in the Options table of the database to the actual 
                                        url of your website. For example, if the site url and home url of the database is http://test.com, but the actual url of your website is https://test.com. 
                                        You’ll need to change the http to https.
                                                                  </p></div>');
        }
    }

    public function get_database_site_url()
    {
        $site_url = site_url();
        global $wpdb;
        $site_url_sql = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name = %s", 'siteurl' ) );
        foreach ( $site_url_sql as $site ){
            $site_url = $site->option_value;
        }
        return untrailingslashit($site_url);
    }

    public function get_database_home_url()
    {
        $home_url = home_url();
        global $wpdb;
        $home_url_sql = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name = %s", 'home' ) );
        foreach ( $home_url_sql as $home ){
            $home_url = $home->option_value;
        }
        return untrailingslashit($home_url);
    }

    public function output_create_staging_site_page()
    {
        self::wpvivid_check_site_url();
        update_option('wpvivid_current_running_staging_task','');
        update_option('wpvivid_staging_task_cancel', false);
        $home_url   = $this->get_database_home_url();
        $admin_url  = admin_url();
        $admin_name = basename($admin_url);
        $admin_name = trim($admin_name, '/');

        $home_path = get_home_path();
        $staging_num = 1;
        $staging_dir = 'mystaging01';
        $staging_content_dir = 'mystaging01';
        $default_staging_site = 'mystaging01';
        while(1){
            $default_staging_site = 'mystaging'.sprintf("%02d", $staging_num);
            $staging_dir = $home_path.$default_staging_site;
            if(!file_exists($staging_dir)){
                break;
            }
            $staging_num++;
        }

        $content_dir = WP_CONTENT_DIR;
        $content_dir = str_replace('\\','/',$content_dir);
        $content_path = $content_dir.'/';
        $staging_num = 1;
        $default_content_staging_site='mystaging01';
        while(1){
            $default_content_staging_site = 'mystaging'.sprintf("%02d", $staging_num);
            $staging_dir = $content_path.$default_content_staging_site;
            if(!file_exists($staging_dir)){
                break;
            }
            $staging_num++;
        }

        global $wpdb;
        $prefix='';
        $site_id=1;
        $base_prefix=$wpdb->base_prefix;
        while(1)
        {
            if($site_id<10)
            {
                $prefix='wpvividstg0'.$site_id.'_';
            }
            else
            {
                $prefix='wpvividstg'.$site_id.'_';
            }

            $sql=$wpdb->prepare("SHOW TABLES LIKE %s;", $wpdb->esc_like($prefix) . '%');
            $result = $wpdb->get_results($sql, OBJECT_K);
            if(empty($result))
            {
                break;
            }
            $site_id++;
        }
        ?>

        <div class="postbox quickbackup-addon">
            <div>
                <div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left;">
                    <img src="<?php echo esc_url(WPVIVID_STAGING_PLUGIN_URL.'includes/images/staging.png'); ?>" style="width:50px;height:50px;">
                </div>
                <div class="wpvivid-element-space-bottom">
                    <div class="wpvivid-text-space-bottom" style="margin-bottom: 0;">This tab allows you to create a staging site. To speed up the process, please try to uncheck those large unessential tables (with a large number of rows) created by plugins, e.g., statics plugins, log plugins, etc.</div>
                </div>
                <div style="clear: both;"></div>
            </div>
            <div style="clear: both;"></div>

            <div>
                <div id="wpvivid_create_staging_step1">
                    <div class="wpvivid-element-space-bottom">
                        <table class="wp-list-table widefat plugins" style="width: 100%;">
                            <tbody>
                            <tr>
                                <td class="column-primary" style="border-bottom:1px solid #f1f1f1;background-color:#f1f1f1;" colspan="2">
                                    <span><strong>Initialize the Staging</strong></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="column-description desc" colspan="2" style="padding: 0 10px;">
                                    <div style="padding: 10px 0;">
                                        <div class="wpvivid-element-space-bottom">
                                            <label>
                                                <input type="text" id="wpvivid_staging_path" placeholder="<?php esc_attr_e($default_staging_site); ?>" value="<?php esc_attr_e($default_staging_site); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9]/g,'')" onpaste="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')" /> Name the folder, your staging site will be installed to the directory. By default: <?php echo $default_staging_site; ?>
                                            </label>
                                        </div>
                                        <div>
                                            <label>
                                                <input type="text" id="wpvivid_staging_table_prefix" placeholder="<?php esc_attr_e($prefix); ?>" value="<?php esc_attr_e($prefix); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9-_]/g,'')" onpaste="value=value.replace(/[^\a-\z\A-\Z0-9-_]/g,'')" title="Table Prefix"> is named as the table prefix, By default: <?php echo $prefix; ?>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="column-description desc" colspan="2" style="padding: 0 10px;">
                                    <div>
                                        <div style="padding:0 0 10px 0;">
                                            <div style="border-left:4px solid #00a0d2;padding-left:10px;float:left;">
                                                <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>Staging Site URL:</strong></span><span class="wpvivid-element-space-right"><?php echo $home_url; ?>/<span class="wpvivid-staging-site-name" style="margin: 0 !important;"><?php echo $default_staging_site; ?></span></span></div>
                                                <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>Staging Site Admin URL:</strong></span><span class="wpvivid-element-space-right"><?php echo $home_url; ?>/<span class="wpvivid-staging-site-name" style="margin: 0 !important;"><?php echo $default_staging_site; ?></span>/<?php echo $admin_name; ?></span></div>
                                            </div>
                                            <div style="clear:both"></div>
                                            <div style="border-left:4px solid #00a0d2;padding-left:10px;float:left;">
                                                <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>Database:</strong></span><span class="wpvivid-element-space-right wpvivid-staging-additional-database-name-display"><?php echo DB_NAME; ?></span></div>
                                                <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>Table Prefix:</strong></span><span class="wpvivid-element-space-right wpvivid-staging-table-prefix-display"><?php echo $prefix; ?></span></div>
                                                <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>Site Directory:</strong></span><span class="wpvivid-element-space-right"><?php echo get_home_path(); ?><span class="wpvivid-staging-site-name" style="margin: 0 !important;"><?php echo $default_staging_site; ?></span></span></div>
                                            </div>
                                            <div style="clear:both"></div>
                                        </div>
                                        <fieldset>
                                            <div><strong>Choose a database for the staging site</strong></div>
                                            <div style="margin: auto;">
                                                <div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left;">
                                                    <label>
                                                        <input type="radio" name="choose_staging_db" value="0" checked />
                                                        <span>Share the same database with your live site (recommended)</span>
                                                    </label>
                                                </div>
                                                <div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left;">
                                                    <label>
                                                        <input type="radio" name="choose_staging_db" value="1" />
                                                        <span>Install the staging site to another database</span>
                                                    </label>
                                                </div>
                                                <div style="clear: both;"></div>
                                            </div>
                                        </fieldset>
                                        <div class="wpvivid-element-space-bottom" id="wpvivid_additional_database_account" style="padding:10px;border:1px solid #f1f1f1; border-radius:10px; display: none;">
                                            <form>
                                            <label><input type="text" class="wpvivid-additional-database-name" autocomplete="off" placeholder="Database" title="Database Name"></label>
                                            <label><input type="text" class="wpvivid-additional-database-user" autocomplete="off" placeholder="Username" title="Database Username"></label>
                                            <label><input type="password" class="wpvivid-additional-database-pass" autocomplete="off" placeholder="Password" title="The Password of the Database Username"></label>
                                            <label><input type="text" class="wpvivid-additional-database-host" autocomplete="off" placeholder="localhost" title="Database Host"></label>
                                            <label><input type="button" id="wpvivid_connect_additional_database" onclick="wpvivid_additional_database_connect_test();" value="Test Connection"></label>
                                            </form>
                                        </div>
                                        <fieldset>
                                            <div><strong>Choose which directory to install the staging site</strong></div>
                                            <div style="margin: auto;">
                                                <div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left;">
                                                    <label>
                                                        <input type="radio" name="choose_staging_dir" value="0" checked />
                                                        <span>Install the staging site to the root directory of the current site</span>
                                                    </label>
                                                </div>
                                                <div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left;">
                                                    <label>
                                                        <input type="radio" name="choose_staging_dir" value="1" />
                                                        <span>Install the staging site to the wp-content directory of the current site</span>
                                                    </label>
                                                </div>
                                                <div style="clear: both;"></div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="wpvivid_choose_staging_content">
                        <table class="wp-list-table widefat plugins" style="width: 100%; margin-bottom: 10px;">
                            <tbody>
                            <tr>
                                <td class="column-primary" style="border-bottom:1px solid #f1f1f1;background-color:#f1f1f1;" colspan="2">
                                    <span><strong><?php _e('Choose what to copy to the staging site', 'wpvivid'); ?></strong></span>
                                </td>
                            </tr>
                            <?php
                            if(is_multisite())
                            {
                                ?>
                                <tr id="wpvividstg_select_backup_content">
                                    <td>
                                        <fieldset>
                                            <div style="margin: auto;">
                                                <div class="wpvivid-element-space-right" style="float: left;">
                                                    <label>
                                                        <input type="radio" name="choose_backup_content" value="2" checked/>
                                                        <span>Create a staging site for a single MU subsite <strong>(both subdomain and subdirectory Multisite supported)</strong></span>
                                                    </label>
                                                </div>
                                                <div style="clear: both;"></div>
                                                <div class="wpvivid-element-space-right" style="float: left;">
                                                    <label>
                                                        <input type="radio" name="choose_backup_content" value="0" />
                                                        <span>Create a staging site for the entire MU network <strong>(only subdirectory Multisite supported)</strong></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                                <?php
                                $subsites = get_sites();
                                $list=array();
                                $listex=array();
                                $main_site_id='0';
                                $main_site_name='';
                                $main_site_title=get_option( 'blogname' );
                                $main_site_description=get_option( 'blogdescription' );

                                foreach ($subsites as $subsite)
                                {
                                    if(is_main_site(get_object_vars($subsite)["blog_id"]))
                                    {
                                        $main_site_id=get_object_vars($subsite)["blog_id"];
                                        $main_site_name = get_object_vars($subsite)["domain"].get_object_vars($subsite)["path"];
                                    }
                                    else
                                    {
                                        $list[]=$subsite;
                                    }
                                    $listex[]=$subsite;
                                }
                                $core_descript = 'These are the essential files for creating a staging site.';
                                $db_descript = 'The tables created by WordPress are required for the staging site.';
                                $themes_plugins_descript = 'All the plugins and themes files used by the MU network.';
                                $uploads_descript = 'The folder where images and media files of main site are stored by default.';
                                $contents_descript = 'All the folders under wp-content you want to copy to the staging site, except for the Uploads folder.';
                                ?>
                                <tr id="wpvividstg_single_site_backup_content">
                                    <td style="padding-top: 0;">
                                        <div id="wpvivid_mu_single_staging_site_step1">
                                            <p>
                                                Choose the subsite for which you want to create a staging site.
                                                <span style="float: right;margin-bottom: 6px">
                                                <label class="screen-reader-text" for="site-search-input">Search A Subsite:</label>
                                                <input type="search" id="wpvivid-mu-single-site-search-input" name="s" value="">
                                                <input type="submit" id="wpvivid-mu-single-search-submit" class="button" value="Search A Subsite">
                                            </span>
                                            </p>
                                            <div id="wpvivid_mu_single_staging_site_list">
                                                <?php
                                                $mu_site_list = new WPvivid_Staging_MU_Single_Site_List();
                                                $mu_site_list ->set_parent('wpvivid_mu_single_staging_site_list');
                                                $mu_site_list->set_list($listex,'mu_single_site');
                                                $mu_site_list->prepare_items();
                                                $mu_site_list ->display();
                                                ?>
                                                <br>
                                                <div class="wpvivid-element-space-bottom">
                                                    <input type="button" id="wpvivid_next_single_site_staging" class="button button-primary" value="Next Step" />
                                                </div>
                                            </div>
                                        </div>

                                        <div id="wpvivid_mu_single_staging_site_step2">
                                            <div id="wpvivid_custom_mu_single_staging_list">
                                                <?php
                                                $custom_mu_staging_list = new WPvivid_Custom_MU_Staging_List_Ex();
                                                $custom_mu_staging_list ->set_parent_id('wpvivid_custom_mu_single_staging_list');
                                                $custom_mu_staging_list ->set_staging_home_path();
                                                $custom_mu_staging_list ->display_rows();
                                                $custom_mu_staging_list ->load_js();
                                                ?>
                                                <br>
                                                <div class="wpvivid-element-space-bottom">
                                                    <input type="button" id="wpvivid_back_single_site_staging" class="button button-primary" value="Previous Step" />
                                                    <input type="button" id="wpvivid_mu_single_create_staging" class="button button-primary" value="Create Now" />
                                                </div>
                                                <div>Note: Please don't refresh the page while creating a staging site.</div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="wpvividstg_quick_select_backup_content">
                                    <td style="padding-top: 0;">
                                        <label class="wpvivid-element-space-bottom" style="width:100%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap; padding-top: 3px;">
                                            <input type="checkbox" option="wpvividstg_mu_sites" name="mu_site" value="<?php echo $main_site_id?>" checked disabled/>
                                            MU Files and Database
                                        </label>
                                        <div class="wpvivid-element-space-bottom">
                                            <div id="wpvivid_custom_mu_staging_list">
                                                <?php
                                                $custom_mu_staging_list = new WPvivid_Custom_MU_Staging_List_Ex();
                                                $custom_mu_staging_list ->set_parent_id('wpvivid_custom_mu_staging_list');
                                                $custom_mu_staging_list ->set_staging_home_path();
                                                $custom_mu_staging_list ->display_rows();
                                                $custom_mu_staging_list ->load_js();
                                                ?>
                                            </div>
                                            <div style="clear: both;"></div>
                                        </div>
                                        <p>Select the subsites you wish to copy to the staging site</p>
                                        <div style="clear: both;"></div>
                                        <p>
                                            <label>
                                                <input type="checkbox" option="wpvividstg_mu_sites" name="mu_all_site" checked />
                                                Select all subsites with their database tables and folders
                                            </label>
                                            <span style="float: right;margin-bottom: 6px">
                                                <label class="screen-reader-text" for="site-search-input">Search A Subsite:</label>
                                                <input type="search" id="wpvivid-mu-site-search-input" name="s" value="">
                                                <input type="submit" id="wpvivid-mu-search-submit" class="button" value="Search A Subsite">
                                            </span>
                                        </p>
                                        <div id="wpvivid_mu_staging_site_list" style="pointer-events: none; opacity: 0.4;">
                                            <?php
                                            $mu_site_list = new WPvivid_Staging_MU_Site_List();
                                            $mu_site_list ->set_parent('wpvivid_mu_staging_site_list');
                                            $mu_site_list->set_list($list,'mu_site');
                                            $mu_site_list->prepare_items();
                                            $mu_site_list ->display();
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            else
                            {
                                ?>
                                <tr id="wpvividstg_custom_backup_content">
                                    <td class="column-description desc" colspan="2" style="padding: 0;">
                                        <div id="wpvivid_custom_staging_list">
                                            <?php
                                            $custom_staging_list = new WPvivid_Custom_Staging_List_Ex();
                                            $custom_staging_list ->set_parent_id('wpvivid_custom_staging_list');
                                            $custom_staging_list ->set_staging_home_path();
                                            $custom_staging_list ->display_rows();
                                            $custom_staging_list ->load_js();
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>

                            </tbody>
                        </table>
                        <?php
                        if(is_multisite())
                        {
                            ?>
                            <div id="wpvivid_mu_create_staging_content">
                                <div class="wpvivid-element-space-bottom">
                                    <input type="button" id="wpvivid_mu_create_staging" class="button button-primary" value="Create Now" />
                                </div>
                                <div>Note: Please don't refresh the page while creating a staging site.</div>
                            </div>
                            <?php
                        }
                        else
                        {
                            ?>
                            <div id="wpvivid_create_staging_content">
                                <div class="wpvivid-element-space-bottom">
                                    <input type="button" id="wpvivid_create_staging" class="button button-primary" value="Create Now" />
                                </div>
                                <div>Note: Please don't refresh the page while creating a staging site.</div>
                            </div>
                            <?php
                        }
                        ?>

                    </div>
                    <div style="clear: both;"></div>
                </div>
                <div id="wpvivid_create_staging_step2" style="display: none;">
                    <div class="wpvivid-element-space-bottom">
                        <input class="button button-primary" type="button" id="wpvivid_staging_cancel" value="Cancel" />
                    </div>
                    <div class="postbox wpvivid-staging-log wpvivid-element-space-bottom" id="wpvivid_staging_log" style="margin-bottom: 0;"></div>
                    <div class="action-progress-bar" style="margin: 10px 0 0 0; !important;">
                        <div class="action-progress-bar-percent" id="wpvivid_staging_progress_bar" style="height:24px;line-height:24px;width:0;">
                            <div style="float: left; margin-left: 4px;">0</div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            <?php
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['basedir'];
            $upload_path = str_replace('\\','/',$upload_path);
            $upload_path = $upload_path.'/';
            $content_dir = WP_CONTENT_DIR;
            $content_path = str_replace('\\','/',$content_dir);
            $content_path = $content_path.'/';
            $home_path = str_replace('\\','/', get_home_path());
            if(is_multisite()){
            ?>

            jQuery('#wpvivid_custom_mu_single_staging_list').find('.wpvivid-wordpress-core').html('WordPress Core');
            jQuery('#wpvivid_custom_mu_single_staging_list').find('.database-desc').html('All the tables that belong to the subsite');
            jQuery('#wpvivid_custom_mu_single_staging_list').find('.themes-plugins-desc').html('All the plugins and themes files used by the MU network. Plugins and themes activated on the subsite will be copied to the staging site by default.');
            jQuery('#wpvivid_custom_mu_single_staging_list').find('.uploads-desc').html('Files under the "uploads" folder that the staging site needs.');

            jQuery('#wpvivid_custom_mu_staging_list').find('.wpvivid-wordpress-core').html('WordPress MU Core');
            jQuery('#wpvivid_custom_staging_list').find('.wpvivid-wordpress-core').html('WordPress MU Core');
            jQuery('#wpvivid_custom_staging_list').find('.database-desc').html('All the tables in the WordPress MU database. The tables created by WordPress MU are required for the staging site. The tables created by themes or plugins are optional.');
            jQuery('#wpvivid_custom_staging_list').find('.themes-plugins-desc').html('All the plugins and themes files used by the MU network. The activated plugins and themes will be copied to the staging site by default. A child theme must be copied if it exists.');
            jQuery('#wpvivid_custom_staging_list').find('.uploads-desc').html('The folder where images and media files of the MU network are stored by default. All files will be copied to the staging site by default. You can exclude folders you do not want to copy.');
            jQuery('#wpvivid_mu_staging_site_list').find('input:checkbox').each(function(){
                jQuery(this).prop('checked', true);
            });
            jQuery('#wpvividstg_quick_select_backup_content').on("click",'input:checkbox[option=wpvividstg_mu_sites][name=mu_all_site]',function()
            {
                if(jQuery('input:checkbox[option=wpvividstg_mu_sites][name=mu_all_site]').prop('checked'))
                {
                    jQuery('#wpvivid_mu_staging_site_list').find('input:checkbox').each(function(){
                        jQuery(this).prop('checked', true);
                    });
                    jQuery('#wpvivid_mu_staging_site_list').css({'pointer-events': 'none', 'opacity': '0.4'});
                }
                else{
                    jQuery('#wpvivid_mu_staging_site_list').find('input:checkbox').each(function(){
                        jQuery(this).prop('checked', false);
                    });
                    jQuery('#wpvivid_mu_staging_site_list').css({'pointer-events': 'auto', 'opacity': '1'});
                }
            });
            <?php
            }
            ?>
            load_js('wpvivid_custom_staging_list', false, '<?php echo $upload_path; ?>', '<?php echo $content_path; ?>', '<?php echo $home_path; ?>');
            load_js('wpvivid_custom_mu_staging_list', false, '<?php echo $upload_path; ?>', '<?php echo $content_path; ?>', '<?php echo $home_path; ?>');
            load_js('wpvivid_custom_mu_single_staging_list', false, '<?php echo $upload_path; ?>', '<?php echo $content_path; ?>', '<?php echo $home_path; ?>');
            jQuery('#wpvivid_create_staging_step1').on("keyup", '#wpvivid_staging_path', function()
            {
                var staging_path = jQuery('#wpvivid_staging_path').val();
                if(staging_path !== ''){
                    jQuery('.wpvivid-staging-site-name').html(staging_path);
                }
                else{
                    jQuery('.wpvivid-staging-site-name').html('*');
                }
            });

            jQuery('#wpvivid_create_staging_step1').on("click", 'input:radio[name=choose_staging_db]', function(){
                if(jQuery(this).prop('checked')){
                    var value = jQuery(this).val();
                    if(value === '0'){
                        jQuery('#wpvivid_additional_database_account').hide();
                        jQuery('.wpvivid-staging-additional-database-name-display').html('<?php echo DB_NAME; ?>');
                    }
                    else{
                        jQuery('#wpvivid_additional_database_account').show();
                        var additional_db_name = jQuery('.wpvivid-additional-database-name').val();
                        if(additional_db_name !== ''){
                            jQuery('.wpvivid-staging-additional-database-name-display').html(additional_db_name);
                        }
                        else{
                            jQuery('.wpvivid-staging-additional-database-name-display').html('*');
                        }
                        wpvivid_additional_database_table_prefix();
                    }
                }
            });

            var default_staging_site = '<?php echo $default_staging_site; ?>';
            var default_content_staging_site = '<?php echo $default_content_staging_site; ?>';
            var is_mu='<?php echo is_multisite(); ?>';
            jQuery('#wpvivid_create_staging_step1').on("click", 'input:radio[name=choose_staging_dir]', function()
            {
                if(jQuery(this).prop('checked'))
                {
                    var value = jQuery(this).val();

                    if(value === '0')
                    {
                        jQuery('#wpvivid_staging_path').val(default_staging_site);
                        var staging_path = jQuery('#wpvivid_staging_path').val();
                        if(staging_path !== '')
                        {
                            jQuery('.wpvivid-staging-site-name').html(staging_path);
                        }
                        else{
                            jQuery('.wpvivid-staging-site-name').html('*');
                        }
                    }
                    else
                    {
                        jQuery('#wpvivid_staging_path').val(default_content_staging_site);
                        var staging_path = jQuery('#wpvivid_staging_path').val();
                        if(staging_path !== '')
                        {
                            jQuery('.wpvivid-staging-site-name').html('wp-content/'+staging_path);
                        }
                        else{
                            jQuery('.wpvivid-staging-site-name').html('wp-content/*');
                        }
                    }
                }
            });

            jQuery('#wpvivid_create_staging_step1').on("keyup", '.wpvivid-additional-database-name', function(){
                var additional_db_name = jQuery(this).val();
                if(additional_db_name !== ''){
                    jQuery('.wpvivid-staging-additional-database-name-display').html(additional_db_name);
                }
                else{
                    jQuery('.wpvivid-staging-additional-database-name-display').html('*');
                }
            });

            jQuery('#wpvivid_create_staging_step1').on("keyup", '#wpvivid_staging_table_prefix', function(){
                wpvivid_additional_database_table_prefix();
            });

            function wpvivid_additional_database_table_prefix(){
                var additional_db_prefix = jQuery('#wpvivid_create_staging_step1').find('#wpvivid_staging_table_prefix').val();
                if(additional_db_prefix !== ''){
                    jQuery('.wpvivid-staging-table-prefix-display').html(additional_db_prefix);
                }
                else{
                    jQuery('.wpvivid-staging-table-prefix-display').html('*');
                }
            }

            jQuery('#wpvivid_create_staging_step2').on("click", '#wpvivid_staging_cancel', function(){
                wpvivid_staging_cancel();
            });

            function wpvivid_staging_cancel(){
                var ajax_data = {
                    'action': 'wpvividstg_cancel_staging'
                };
                jQuery('#wpvivid_staging_cancel').css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function(data){

                }, function(XMLHttpRequest, textStatus, errorThrown) {
                    jQuery('#wpvivid_staging_cancel').css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = wpvivid_output_ajaxerror('cancelling the staging', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            jQuery('#wpvivid_create_staging').click(function()
            {
                var descript = 'Click OK to start creating the staging site.';
                var ret = confirm(descript);
                if(ret === true){
                    jQuery('#wpvivid_staging_notice').hide();
                    wpvivid_start_staging();
                }
            });

            jQuery('#wpvivid_mu_create_staging').click(function()
            {
                var descript = 'Click OK to start creating the staging site.';
                var ret = confirm(descript);
                if(ret === true){
                    jQuery('#wpvivid_staging_notice').hide();
                    wpvivid_start_staging();
                }
            });

            jQuery('#wpvivid_mu_single_create_staging').click(function()
            {
                var descript = 'Click OK to start creating the staging site.';
                var ret = confirm(descript);
                if(ret === true){
                    jQuery('#wpvivid_staging_notice').hide();
                    wpvivid_start_staging();
                }
            });

            function wpvivid_recreate_staging(){
                jQuery('#wpvivid_choose_staging_content').show();
                jQuery('#wpvivid_create_staging_step2').hide();
            }

            function wpvivid_create_custom_json(parent_id){
                var json = {};
                jQuery('#'+parent_id).find('.wpvivid-custom-check').each(function(){
                    if(jQuery(this).hasClass('wpvivid-custom-core-check')){
                        json['core_list'] = Array();
                        if(jQuery(this).prop('checked')){
                            json['core_check'] = '1';
                        }
                        else{
                            json['core_check'] = '0';
                        }
                    }
                    else if(jQuery(this).hasClass('wpvivid-custom-database-check')){
                        json['database_list'] = Array();
                        if(jQuery(this).prop('checked')){
                            json['database_check'] = '1';
                            jQuery('#'+parent_id).find('input:checkbox[name=Database]').each(function(){
                                if(!jQuery(this).prop('checked')){
                                    json['database_list'].push(jQuery(this).val());
                                }
                            });
                        }
                        else{
                            json['database_check'] = '0';
                        }
                    }
                    else if(jQuery(this).hasClass('wpvivid-custom-themes-plugins-check')){
                        json['themes_list'] = Array();
                        json['plugins_list'] = Array();
                        if(jQuery(this).prop('checked')){
                            json['themes_check'] = '0';
                            json['plugins_check'] = '0';
                            jQuery('#'+parent_id).find('input:checkbox[option=themes][name=Themes]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    json['themes_check'] = '1';
                                }
                                else{
                                    json['themes_list'].push(jQuery(this).val());
                                }
                            });
                            jQuery('#'+parent_id).find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                                if(jQuery(this).prop('checked')) {
                                    json['plugins_check'] = '1';
                                }
                                else{
                                    json['plugins_list'].push(jQuery(this).val());
                                }
                            });
                        }
                        else{
                            json['themes_check'] = '0';
                            json['plugins_check'] = '0';
                        }
                    }
                    else if(jQuery(this).hasClass('wpvivid-custom-uploads-check')){
                        json['uploads_list'] = {};
                        if(jQuery(this).prop('checked')){
                            json['uploads_check'] = '1';
                            jQuery('#'+parent_id).find('.wpvivid-custom-exclude-uploads-list ul').find('li div:eq(1)').each(function(){
                                var folder_name = this.innerHTML;
                                json['uploads_list'][folder_name] = {};
                                json['uploads_list'][folder_name]['name'] = folder_name;
                                json['uploads_list'][folder_name]['type'] = jQuery(this).prev().get(0).classList.item(0);
                            });
                            json['upload_extension'] = jQuery('#'+parent_id).find('.wpvivid-uploads-extension').val();
                        }
                        else{
                            json['uploads_check'] = '0';
                        }
                    }
                    else if(jQuery(this).hasClass('wpvivid-custom-content-check')){
                        json['content_list'] = {};
                        if(jQuery(this).prop('checked')){
                            json['content_check'] = '1';
                            jQuery('#'+parent_id).find('.wpvivid-custom-exclude-content-list ul').find('li div:eq(1)').each(function(){
                                var folder_name = this.innerHTML;
                                json['content_list'][folder_name] = {};
                                json['content_list'][folder_name]['name'] = folder_name;
                                json['content_list'][folder_name]['type'] = jQuery(this).prev().get(0).classList.item(0);
                            });
                            json['content_extension'] = jQuery('#'+parent_id).find('.wpvivid-content-extension').val();
                        }
                        else{
                            json['content_check'] = '0';
                        }
                    }
                    else if(jQuery(this).hasClass('wpvivid-custom-additional-file-check')){
                        json['additional_file_list'] = {};
                        if(jQuery(this).prop('checked')){
                            json['additional_file_check'] = '1';
                            jQuery('#'+parent_id).find('.wpvivid-custom-include-additional-file-list ul').find('li div:eq(1)').each(function(){
                                var folder_name = this.innerHTML;
                                json['additional_file_list'][folder_name] = {};
                                json['additional_file_list'][folder_name]['name'] = folder_name;
                                json['additional_file_list'][folder_name]['type'] = jQuery(this).prev().get(0).classList.item(0);
                            });
                            json['additional_file_extension'] = jQuery('#'+parent_id).find('.wpvivid-additional-file-extension').val();
                        }
                        else{
                            json['additional_file_check'] = '0';
                        }
                    }
                });
                return json;
            }

            function wpvivid_create_staging_lock_unlock(action){
                if(action === 'lock'){
                    jQuery('#wpvivid_create_staging_step1').find('input').attr('disabled', true);
                    jQuery('#wpvivid_staging_list').find('div.wpvivid-delete-staging-site').css({'pointer-events': 'none', 'opacity': '0.4'});
                }
                else{
                    jQuery('#wpvivid_create_staging_step1').find('input').attr('disabled', false);
                    jQuery('#wpvivid_staging_list').find('div.wpvivid-delete-staging-site').css({'pointer-events': 'auto', 'opacity': '1'});
                }
            }

            function wpvivid_check_staging_additional_folder_valid(parent_id){
                var check_status = false;
                if(jQuery('#'+parent_id).find('.wpvivid-custom-additional-file-check').prop('checked')){
                    jQuery('#'+parent_id).find('.wpvivid-custom-include-additional-file-list ul').find('li div:eq(1)').each(function () {
                        check_status = true;
                    });
                    if(check_status === false){
                        alert('Please select at least one item under the additional files/folder option, or deselect the option.');
                    }
                }
                else{
                    check_status = true;
                }
                return check_status;
            }

            function wpvivid_start_staging()
            {
                var path=jQuery('#wpvivid_staging_path').val();
                var table_prefix=jQuery('#wpvivid_staging_table_prefix').val();
                if(path !== '')
                {
                    if(table_prefix !== '')
                    {
                        var additional_database_json = {};

                        var additional_database_option = '0';
                        jQuery('#wpvivid_create_staging_step1').find('input:radio[name=choose_staging_db]').each(function ()
                        {
                            if (jQuery(this).prop('checked')) {
                                additional_database_option = jQuery(this).val();
                            }
                        });
                        var staging_root_dir='0';
                        jQuery('#wpvivid_create_staging_step1').find('input:radio[name=choose_staging_dir]').each(function ()
                        {
                            if (jQuery(this).prop('checked'))
                            {
                                staging_root_dir = jQuery(this).val();
                            }
                        });
                        if (additional_database_option === '1')
                        {
                            additional_database_json['additional_database_check'] = '1';
                            additional_database_json['additional_database_info'] = {};
                            additional_database_json['additional_database_info']['db_user'] = jQuery('.wpvivid-additional-database-user').val();
                            additional_database_json['additional_database_info']['db_pass'] = jQuery('.wpvivid-additional-database-pass').val();
                            additional_database_json['additional_database_info']['db_host'] = jQuery('.wpvivid-additional-database-host').val();
                            additional_database_json['additional_database_info']['db_name'] = jQuery('.wpvivid-additional-database-name').val();
                            if (additional_database_json['additional_database_info']['db_name'] === '') {
                                alert('Database Name is required.');
                                return;
                            }
                            if (additional_database_json['additional_database_info']['db_user'] === '') {
                                alert('Database User is required.');
                                return;
                            }
                            if (additional_database_json['additional_database_info']['db_host'] === '') {
                                alert('Database Host is required.');
                                return;
                            }
                        }
                        else {
                            additional_database_json['additional_database_check'] = '0';
                        }
                        var additional_database_info = JSON.stringify(additional_database_json);

                        var check_status = wpvivid_check_staging_additional_folder_valid('wpvivid_custom_staging_list');
                        if (check_status)
                        {
                            var ajax_data =
                                {
                                'action': 'wpvividstg_check_staging_dir',
                                'root_dir':staging_root_dir,
                                'path': path,
                                'table_prefix': table_prefix
                            };

                            wpvivid_post_request(ajax_data, function (data)
                            {
                                var jsonarray = jQuery.parseJSON(data);
                                if (jsonarray.result === 'failed')
                                {
                                    alert(jsonarray.error);
                                }
                                else
                                {
                                    jQuery('#wpvivid_staging_log').html("");
                                    jQuery('#wpvivid_staging_progress_bar').css('width', '0%');
                                    jQuery('#wpvivid_staging_progress_bar').find('div').eq(0).html('0%');
                                    var custom_dir_json = wpvivid_create_custom_json('wpvivid_custom_staging_list');
                                    var custom_dir = JSON.stringify(custom_dir_json);
                                    var mu_quick_select=false;
                                    var mu_single_select=false;
                                    var mu_site_list='';
                                    var mu_single_site='';
                                    var check_select = true;
                                    if(is_mu)
                                    {
                                        if(jQuery('#wpvividstg_select_backup_content').find('input:radio[name=choose_backup_content][value="0"]').prop('checked'))
                                        {
                                            custom_dir_json = wpvivid_create_custom_json('wpvivid_custom_mu_staging_list');
                                            custom_dir = JSON.stringify(custom_dir_json);
                                            mu_quick_select=true;
                                            var json = {};
                                            //wpvividstg_mu_sites
                                            if(jQuery('#wpvividstg_mu_sites').prop('checked'))
                                            {

                                            }
                                            json['mu_site_list']=Array();
                                            jQuery('input[name=mu_site][type=checkbox]').each(function(index, value)
                                            {
                                                if(jQuery(value).prop('checked'))
                                                {
                                                    var subjson = {};
                                                    subjson['id']=jQuery(value).val();
                                                    if(jQuery('input:checkbox[name=mu_site_tables][value='+jQuery(value).val()+']').prop('checked'))
                                                    {
                                                        subjson['tables']=1;
                                                    }
                                                    else
                                                    {
                                                        subjson['tables']=0;
                                                    }
                                                    if(jQuery('input:checkbox[name=mu_site_folders][value='+jQuery(value).val()+']').prop('checked'))
                                                    {
                                                        subjson['folders']=1;
                                                    }
                                                    else
                                                    {
                                                        subjson['folders']=0;
                                                    }
                                                    json['mu_site_list'].push(subjson);
                                                }
                                            });

                                            if(jQuery('input:checkbox[option=wpvividstg_mu_sites][name=mu_all_site]').prop('checked'))
                                            {
                                                json['all_site']=1;
                                            }
                                            else
                                            {
                                                json['all_site']=0;
                                            }
                                            mu_site_list= JSON.stringify(json);
                                        }
                                        else if(jQuery('#wpvividstg_select_backup_content').find('input:radio[name=choose_backup_content][value="2"]').prop('checked'))
                                        {
                                            custom_dir_json = wpvivid_create_custom_json('wpvivid_custom_mu_single_staging_list');
                                            custom_dir = JSON.stringify(custom_dir_json);
                                            var json = {};
                                            jQuery('input[name=mu_single_site][type=checkbox]').each(function(index, value)
                                            {
                                                if(jQuery(value).prop('checked'))
                                                {
                                                    json['id']=jQuery(value).val();
                                                    mu_single_select=true;
                                                }
                                            });
                                            mu_single_site= JSON.stringify(json);
                                            if(mu_single_select!=true)
                                            {
                                                alert('You must select a site before creating staging.');
                                                return;
                                            }
                                        }
                                    }
                                    wpvivid_create_staging_lock_unlock('lock');

                                    var ajax_data = {
                                        'action': 'wpvividstg_start_staging',
                                        'path': path,
                                        'table_prefix': table_prefix,
                                        'custom_dir': custom_dir,
                                        'mu_quick_select':mu_quick_select,
                                        'mu_site_list':mu_site_list,
                                        'mu_single_select':mu_single_select,
                                        'mu_single_site':mu_single_site,
                                        'additional_db': additional_database_info,
                                        'root_dir':staging_root_dir
                                    };

                                    jQuery('#wpvivid_choose_staging_content').hide();
                                    jQuery('#wpvivid_create_staging_step2').show();
                                    wpvivid_post_request(ajax_data, function (data)
                                    {
                                        setTimeout(function () {
                                            wpvivid_get_staging_progress();
                                        }, staging_requet_timeout);
                                    }, function (XMLHttpRequest, textStatus, errorThrown)
                                    {
                                        jQuery('#wpvivid_choose_staging_content').hide();
                                        jQuery('#wpvivid_create_staging_step2').show();
                                        setTimeout(function () {
                                            wpvivid_get_staging_progress();
                                        }, staging_requet_timeout);
                                    });
                                }
                            }, function (XMLHttpRequest, textStatus, errorThrown) {
                                var error_message = wpvivid_output_ajaxerror('creating staging site', textStatus, errorThrown);
                                alert(error_message);
                            });
                        }
                    }
                    else{
                        alert('Table Prefix is required.');
                    }
                }
                else{
                    alert('A site name is required.');
                }
            }

            function wpvivid_restart_staging()
            {
                var ajax_data = {
                    'action':'wpvividstg_start_staging',
                };

                wpvivid_post_request(ajax_data, function(data)
                {
                    setTimeout(function()
                    {
                        wpvivid_get_staging_progress();
                    }, staging_requet_timeout);
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    setTimeout(function()
                    {
                        wpvivid_get_staging_progress();
                    }, staging_requet_timeout);
                });
            }

            function wpvivid_get_staging_progress()
            {
                console.log(staging_requet_timeout);
                var ajax_data = {
                    'action':'wpvividstg_get_staging_progress',
                };

                wpvivid_post_request(ajax_data, function(data)
                {
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            var log_data = jsonarray.log;
                            jQuery('#wpvivid_staging_log').html("");
                            while (log_data.indexOf('\n') >= 0)
                            {
                                var iLength = log_data.indexOf('\n');
                                var log = log_data.substring(0, iLength);
                                log_data = log_data.substring(iLength + 1);
                                var insert_log = "<div style=\"clear:both;\">" + log + "</div>";
                                jQuery('#wpvivid_staging_log').append(insert_log);
                                var div = jQuery('#wpvivid_staging_log');
                                div[0].scrollTop = div[0].scrollHeight;
                            }
                            jQuery('#wpvivid_staging_progress_bar').css('width', jsonarray.percent + '%');
                            jQuery('#wpvivid_staging_progress_bar').find('div').eq(0).html(jsonarray.percent + '%');
                            if(jsonarray.continue)
                            {
                                if(jsonarray.need_restart)
                                {
                                    wpvivid_restart_staging();
                                }
                                else
                                {
                                    setTimeout(function()
                                    {
                                        wpvivid_get_staging_progress();
                                    }, staging_requet_timeout);
                                }
                            }
                            else{
                                if(typeof jsonarray.completed !== 'undefined' && jsonarray.completed){
                                    jQuery('#wpvivid_staging_cancel').css({'pointer-events': 'auto', 'opacity': '1'});
                                    wpvivid_create_staging_lock_unlock('unlock');
                                    location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=WPvivid_Staging'; ?>';
                                }
                                else if(typeof jsonarray.error !== 'undefined' && jsonarray.error){
                                    wpvivid_create_staging_lock_unlock('unlock');
                                    var insert_log = "<div style=\"clear:both;\"><a style=\"cursor: pointer;\" onclick=\"wpvivid_recreate_staging();\">Create a staging site</a></div>";
                                    jQuery('#wpvivid_staging_log').append(insert_log);
                                    var div = jQuery('#wpvivid_staging_log');
                                    div[0].scrollTop = div[0].scrollHeight;
                                }
                                else if(typeof jsonarray.is_cancel !== 'undefined' && jsonarray.is_cancel){
                                    var staging_site_info = {};
                                    staging_site_info['staging_path'] = jsonarray.staging_path;
                                    staging_site_info['staging_additional_db'] = jsonarray.staging_additional_db;
                                    staging_site_info['staging_additional_db_user'] = jsonarray.staging_additional_db_user;
                                    staging_site_info['staging_additional_db_pass'] = jsonarray.staging_additional_db_pass;
                                    staging_site_info['staging_additional_db_host'] = jsonarray.staging_additional_db_host;
                                    staging_site_info['staging_additional_db_name'] = jsonarray.staging_additional_db_name;
                                    staging_site_info['staging_table_prefix'] = jsonarray.staging_table_prefix;
                                    staging_site_info = JSON.stringify(staging_site_info);
                                    ajax_data = {
                                        'action': 'wpvividstg_delete_cancel_staging_site',
                                        'staging_site_info': staging_site_info
                                    };
                                    wpvivid_post_request(ajax_data, function (data) {
                                        jQuery('#wpvivid_staging_cancel').css({'pointer-events': 'auto', 'opacity': '1'});
                                        wpvivid_create_staging_lock_unlock('unlock');
                                        jQuery('#wpvivid_choose_staging_content').show();
                                        jQuery('#wpvivid_create_staging_step2').hide();
                                        try {
                                            var jsonarray = jQuery.parseJSON(data);
                                            if (jsonarray !== null) {
                                                if (jsonarray.result === 'success') {
                                                }
                                                else {
                                                    alert(jsonarray.error);
                                                }
                                            }
                                            else {
                                            }
                                        }
                                        catch (e) {
                                        }
                                    }, function (XMLHttpRequest, textStatus, errorThrown) {
                                        wpvivid_create_staging_lock_unlock('unlock');
                                        jQuery('#wpvivid_choose_staging_content').show();
                                        jQuery('#wpvivid_create_staging_step2').hide();
                                        var error_message = wpvivid_output_ajaxerror('deleting staging site', textStatus, errorThrown);
                                        alert(error_message);
                                    });
                                }
                                else{
                                    jQuery('#wpvivid_staging_cancel').css({'pointer-events': 'auto', 'opacity': '1'});
                                    wpvivid_create_staging_lock_unlock('unlock');
                                    jQuery('#wpvivid_choose_staging_content').show();
                                    jQuery('#wpvivid_create_staging_step2').hide();
                                }
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            wpvivid_create_staging_lock_unlock('unlock');
                            jQuery('#wpvivid_choose_staging_content').show();
                            jQuery('#wpvivid_create_staging_step2').hide();
                            alert(jsonarray.error);
                        }
                    }
                    catch(err){
                        setTimeout(function()
                        {
                            wpvivid_get_staging_progress();
                        }, 3000);
                    }

                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    setTimeout(function()
                    {
                        wpvivid_get_staging_progress();
                    }, 3000);
                });
            }

            function wpvivid_additional_database_connect_test(){
                var db_user = jQuery('.wpvivid-additional-database-user').val();
                var db_pass = jQuery('.wpvivid-additional-database-pass').val();
                var db_host = jQuery('.wpvivid-additional-database-host').val();
                var db_name = jQuery('.wpvivid-additional-database-name').val();
                if(db_name !== ''){
                    if(db_user !== ''){
                        if(db_host !== ''){
                            var db_json = {};
                            db_json['db_user'] = db_user;
                            db_json['db_pass'] = db_pass;
                            db_json['db_host'] = db_host;
                            db_json['db_name'] = db_name;
                            var db_connect_info = JSON.stringify(db_json);
                            var ajax_data = {
                                'action': 'wpvividstg_test_additional_database_connect',
                                'database_info': db_connect_info
                            };
                            jQuery('#wpvivid_connect_additional_database').css({
                                'pointer-events': 'none',
                                'opacity': '0.4'
                            });
                            wpvivid_post_request(ajax_data, function (data) {
                                jQuery('#wpvivid_connect_additional_database').css({
                                    'pointer-events': 'auto',
                                    'opacity': '1'
                                });
                                try {
                                    var jsonarray = jQuery.parseJSON(data);
                                    if (jsonarray !== null) {
                                        if (jsonarray.result === 'success') {
                                            alert('Connection success.')
                                        }
                                        else {
                                            alert(jsonarray.error);
                                        }
                                    }
                                    else {
                                        alert('Connection Failed. Please check the credentials you entered and try again.');
                                    }
                                }
                                catch (e) {
                                    alert('Connection Failed. Please check the credentials you entered and try again.');
                                }
                            }, function (XMLHttpRequest, textStatus, errorThrown) {
                                jQuery('#wpvivid_connect_additional_database').css({
                                    'pointer-events': 'auto',
                                    'opacity': '1'
                                });
                                jQuery(obj).css({'pointer-events': 'auto', 'opacity': '1'});
                                var error_message = wpvivid_output_ajaxerror('connecting database', textStatus, errorThrown);
                                alert(error_message);
                            });
                        }
                        else{
                            alert('Database Host is required.');
                        }
                    }
                    else{
                        alert('Database User is required.');
                    }
                }
                else{
                    alert('Database Name is required.');
                }
            }

            jQuery('#wpvivid_mu_staging_site_list').on("click",'.first-page',function() {
                wpvivid_get_mu_list('first');
            });

            jQuery('#wpvivid_mu_staging_site_list').on("click",'.prev-page',function() {
                var page=parseInt(jQuery(this).attr('value'));
                wpvivid_get_mu_list(page-1);
            });

            jQuery('#wpvivid_mu_staging_site_list').on("click",'.next-page',function() {
                var page=parseInt(jQuery(this).attr('value'));
                wpvivid_get_mu_list(page+1);
            });

            jQuery('#wpvivid_mu_staging_site_list').on("click",'.last-page',function() {
                wpvivid_get_mu_list('last');
            });

            jQuery('#wpvivid_mu_staging_site_list').on("keypress", '.current-page', function(){
                if(event.keyCode === 13){
                    var page = jQuery(this).val();
                    wpvivid_get_mu_list(page);
                }
            });
            //wpvividstg_mu_sites
            jQuery('#wpvividstg_mu_sites').click(function()
            {

            });

            jQuery('#wpvivid-mu-search-submit').click(function()
            {
                var search = jQuery('#wpvivid-mu-site-search-input').val();
                var ajax_data = {
                    'action': 'wpvivid_get_mu_list',
                    'search':search,
                    'create':true
                };
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_mu_staging_site_list').html('');
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_mu_staging_site_list').html(jsonarray.html);
                        }
                        else
                        {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err)
                    {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('achieving backup', textStatus, errorThrown);
                    alert(error_message);
                });
            });

            function wpvivid_get_mu_list(page)
            {
                if(page==0)
                {
                    page =jQuery('#wpvivid_mu_staging_site_list').find('.current-page').val();
                }
                var search = jQuery('#wpvivid-mu-site-search-input').val();
                var ajax_data = {
                    'action': 'wpvivid_get_mu_list',
                    'search':search,
                    'page':page,
                    'copy':true
                };
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_mu_staging_site_list').html('');
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_mu_staging_site_list').html(jsonarray.html);
                        }
                        else
                        {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err)
                    {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('achieving backup', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            jQuery('#wpvivid_mu_single_staging_site_list').on("click",'.first-page',function() {
                wpvivid_get_mu_single_list('first');
            });

            jQuery('#wpvivid_mu_single_staging_site_list').on("click",'.prev-page',function() {
                var page=parseInt(jQuery(this).attr('value'));
                wpvivid_get_mu_single_list(page-1);
            });

            jQuery('#wpvivid_mu_single_staging_site_list').on("click",'.next-page',function() {
                var page=parseInt(jQuery(this).attr('value'));
                wpvivid_get_mu_single_list(page+1);
            });

            jQuery('#wpvivid_mu_single_staging_site_list').on("click",'.last-page',function() {
                wpvivid_get_mu_single_list('last');
            });

            jQuery('#wpvivid_mu_single_staging_site_list').on("keypress", '.current-page', function(){
                if(event.keyCode === 13){
                    var page = jQuery(this).val();
                    wpvivid_get_mu_single_list(page);
                }
            });

            jQuery('#wpvivid-mu-single-search-submit').click(function()
            {
                var search = jQuery('#wpvivid-mu-single-site-search-input').val();
                var ajax_data = {
                    'action': 'wpvivid_get_mu_list',
                    'search':search,
                    'create':true
                };
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_mu_single_staging_site_list').html('');
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_mu_staging_site_list').html(jsonarray.html);
                        }
                        else
                        {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err)
                    {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('achieving backup', textStatus, errorThrown);
                    alert(error_message);
                });
            });

            function wpvivid_get_mu_single_list(page)
            {
                if(page==0)
                {
                    page =jQuery('#wpvivid_mu_single_staging_site_list').find('.current-page').val();
                }
                var search = jQuery('#wpvivid-mu-single-site-search-input').val();
                var ajax_data = {
                    'action': 'wpvivid_get_mu_list',
                    'search':search,
                    'page':page,
                    'copy':true,
                    'single':true
                };
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_mu_single_staging_site_list').html('');
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_mu_single_staging_site_list').html(jsonarray.html);
                        }
                        else
                        {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err)
                    {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('achieving backup', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            jQuery('#wpvivid_mu_single_staging_site_list').on("click",'[name=mu_single_site]',function()
            {
                //var checked=jQuery(this);

                jQuery('#wpvivid_mu_single_staging_site_list').find('input:checkbox[name=mu_single_site]').prop('checked', false);
                jQuery('#wpvivid_mu_single_staging_site_list').find('input:checkbox[name=mu_single_site_tables]').prop('checked', false);
                jQuery('#wpvivid_mu_single_staging_site_list').find('input:checkbox[name=mu_single_site_folders]').prop('checked', false);
                jQuery(this).prop('checked', true);
                jQuery(this).closest('tr').find('input:checkbox[name=mu_single_site_tables]').prop('checked', true);
                jQuery(this).closest('tr').find('input:checkbox[name=mu_single_site_folders]').prop('checked', true);
            });

            <?php
            if(is_multisite())
            {
                ?>
            jQuery(document).ready(function ()
            {
                //jQuery('#wpvividstg_custom_backup_content').show();wpvivid_mu_single_staging_site_step1
                jQuery('#wpvividstg_quick_select_backup_content').hide();
                wpvivid_single_site_step1();
            });

            jQuery('#wpvivid_next_single_site_staging').click(function()
            {
                var checked=false;
                var site_id='';
                jQuery('input[name=mu_single_site][type=checkbox]').each(function(index, value)
                {
                    if(jQuery(value).prop('checked'))
                    {
                        checked=true;
                        site_id=jQuery(value).val();
                    }
                });

                if(checked!=true)
                {
                    alert('You must choose a subsite to create the staging site.');
                    return;
                }
                wpvivid_get_mu_custom_themes_plugins_info(site_id);
                wpvivid_single_site_step2();
            });

            function wpvivid_get_mu_custom_themes_plugins_info(site_id)
            {
                var ajax_data = {
                    'action': 'wpvividstg_get_custom_themes_plugins_info',
                    'id':'',
                    'subsite':site_id,
                    'is_staging': '0'
                };
                wpvivid_post_request(ajax_data, function (data)
                {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        jQuery('#wpvivid_custom_mu_single_staging_list').find('.wpvivid-custom-themes-plugins-info').html('');
                        jQuery('#wpvivid_custom_mu_single_staging_list').find('.wpvivid-custom-themes-plugins-info').html(jsonarray.html);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    var need_retry_custom_themes = false;
                    archieve_info.src_theme_retry++;
                    var retry_times = archieve_info.src_theme_retry;
                    if(retry_times < 10){
                        need_retry_custom_themes = true;
                    }
                    if(need_retry_custom_themes)
                    {
                        setTimeout(function()
                        {
                            wpvivid_get_mu_custom_themes_plugins_info(site_id);
                        }, 3000);
                    }
                    else
                    {
                        var refresh_btn = '<input type="submit" class="button-primary" value="Refresh" onclick="wpvivid_get_mu_custom_themes_plugins_info(\''+site_id+'\');">';
                        jQuery('#wpvivid_custom_mu_single_staging_list').find('.wpvivid-custom-themes-plugins-info').html('');
                        jQuery('#wpvivid_custom_mu_single_staging_list').find('.wpvivid-custom-themes-plugins-info').html(refresh_btn);
                    }
                });
            }

            jQuery('#wpvivid_back_single_site_staging').click(function()
            {
                wpvivid_single_site_step1();
            });

            function wpvivid_single_site_step1()
            {
                jQuery('#wpvividstg_single_site_backup_content').show();
                jQuery('#wpvivid_mu_single_staging_site_step1').show();
                jQuery('#wpvivid_mu_single_staging_site_step2').hide();
                jQuery('#wpvivid_mu_create_staging_content').hide();
            }

            function wpvivid_single_site_step2()
            {
                jQuery('#wpvividstg_single_site_backup_content').show();
                jQuery('#wpvivid_mu_single_staging_site_step1').hide();
                jQuery('#wpvivid_mu_single_staging_site_step2').show();
                jQuery('#wpvivid_mu_create_staging_content').hide();
            }

            jQuery('#wpvividstg_select_backup_content').on("click", 'input:radio[name=choose_backup_content]', function()
            {
                if(jQuery(this).prop('checked'))
                {
                    var value = jQuery(this).val();
                    if(value === '0')
                    {
                        jQuery('#wpvividstg_quick_select_backup_content').show();
                        jQuery('#wpvividstg_custom_backup_content').hide();
                        jQuery('#wpvividstg_single_site_backup_content').hide();
                        jQuery('#wpvivid_mu_create_staging_content').show();
                    }
                    else if(value === '1')
                    {
                        jQuery('#wpvividstg_quick_select_backup_content').hide();
                        jQuery('#wpvividstg_custom_backup_content').show();
                        jQuery('#wpvividstg_single_site_backup_content').hide();
                        jQuery('#wpvivid_mu_create_staging_content').hide();
                    }
                    else if(value === '2')
                    {
                        jQuery('#wpvividstg_quick_select_backup_content').hide();
                        jQuery('#wpvividstg_custom_backup_content').hide();
                        wpvivid_single_site_step1();
                        //jQuery('#wpvividstg_single_site_backup_content').show();
                    }
                }
            });
                <?php
            }
            ?>
        </script>
        <?php
    }

    public function get_mu_list()
    {
        $this->ajax_check_security('manage_options');
        try {
            $args = array();
            $list = array();

            if(isset($_POST['copy']))
            {
                $copy=$_POST['copy'];
            }
            else if(isset($_POST['create']))
            {
                $copy='true';
            }
            else
            {
                $copy=false;
            }

            if(isset($_POST['search']))
            {
                global $wpdb;
                $s=$_POST['search'];
                if ( empty( $s ) ) {
                    // Nothing to do.
                } elseif ( preg_match( '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $s ) ||
                    preg_match( '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.?$/', $s ) ||
                    preg_match( '/^[0-9]{1,3}\.[0-9]{1,3}\.?$/', $s ) ||
                    preg_match( '/^[0-9]{1,3}\.$/', $s ) ) {
                    // IPv4 address
                    $sql          = $wpdb->prepare( "SELECT blog_id FROM {$wpdb->registration_log} WHERE {$wpdb->registration_log}.IP LIKE %s", $wpdb->esc_like( $s ) . ( ! empty( $wild ) ? '%' : '' ) );
                    $reg_blog_ids = $wpdb->get_col( $sql );

                    if ( $reg_blog_ids ) {
                        $args['site__in'] = $reg_blog_ids;
                    }
                } elseif ( is_numeric( $s ) && empty( $wild ) ) {
                    $args['ID'] = $s;
                } else {
                    $args['search'] = $s;

                    if ( ! is_subdomain_install() ) {
                        $args['search_columns'] = array( 'path' );
                    }
                }
            }

            if($copy==false||$copy=='false')
            {
                $task_id=$_POST['id'];
                $task = new WPvivid_Staging_Task_Ex($task_id);
                $subsites=$task->get_mu_sites($args);
            }
            else
            {
                $subsites=get_sites($args);
            }



            if(isset($_POST['single']))
            {
                $mu_site_list=new WPvivid_Staging_MU_Single_Site_List();

                foreach ($subsites as $subsite)
                {
                    $list[]=$subsite;
                }
            }
            else
            {
                $mu_site_list=new WPvivid_Staging_MU_Site_List();

                foreach ($subsites as $subsite)
                {
                    if(is_main_site(get_object_vars($subsite)["blog_id"]))
                    {
                        continue;
                    }
                    else
                    {
                        $list[]=$subsite;
                    }
                }
            }

            if(isset($_POST['page']))
            {
                $mu_site_list->set_list($list,'mu_site',$_POST['page']);
            }
            else
            {
                $mu_site_list->set_list($list,'mu_site');
            }

            $mu_site_list->prepare_items();
            ob_start();
            $mu_site_list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function wpvivid_staging_site_avail_check()
    {
        $list = get_option('wpvivid_staging_task_list',array());
        foreach ($list as $key => $value){
            if(empty($key)){
                unset($list[$key]);
            }
            if(isset($value['status']['str']) && $value['status']['str'] !== 'completed')
            {
                if(isset($value['options']['restore'])&&$value['options']['restore'])
                {
                    $value['status']['str']='completed';
                    $value['job']=array();
                    $value['doing']=false;
                    $list[$key]=$value;
                    delete_option('wpvivid_staging_site');
                }
                else
                {
                    unset($list[$key]);
                }
            }
        }
        update_option('wpvivid_staging_task_list', $list);
    }

    public function output_staging_sites_list_page()
    {
        $this->wpvivid_staging_site_avail_check();
        ?>
        <div class="postbox quickbackup-addon">
            <div>
                <div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left;">
                    <img src="<?php echo esc_url(WPVIVID_STAGING_PLUGIN_URL.'includes/images/staging.png'); ?>" style="width:50px;height:50px;">
                </div>
                <div class="wpvivid-element-space-bottom">
                    <div class="wpvivid-text-space-bottom" style="margin-bottom: 0;">This section displays all your existing staging sites. You can click on <strong>Copy Staging to Live Site</strong> button to publish a staging site to a live site.</div>
                </div>
                <div style="clear: both;"></div>
            </div>
            <div style="clear: both;"></div>

            <div>
                <div>
                    <input type="button" class="button button-primary" id="wpvivid_switch_create_staging_page" value="Create A Staging Site" />
                </div>
                <div class="wpvivid-backup-tips" style="background: #fff; border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;">
                    <div style="float: left;">
                        <div style="padding: 10px;">
                            <strong><?php _e('Note: ', 'wpvivid'); ?></strong>
                            <?php _e('Please temporarily deactivate cache plugins before creating a staging site to rule out possibilities of unknown failures.', 'wpvivid'); ?>
                        </div>
                    </div>
                    <small>
                        <div class="wpvivid_tooltip wpvivid-element-space-bottom" style="float: left; margin-top: 10px;">?
                            <div class="wpvivid_tooltiptext">WPvivid Staging will automatically exclude some most-used cache plugins when you create a staging site,
                                but considering that there are hundreds of cache plugins on the market, we would recommend temporarily deactivating cache plugins before creating
                                a staging site to rule out possibilities of unknown failures.</div>
                        </div>
                    </small>
                    <div style="clear: both;"></div>
                </div>
                <div style="clear: both;"></div>
                <?php
                $list = get_option('wpvivid_staging_task_list',array());
                if(!empty($list))
                {
                    ?>
                    <div id="wpvivid_staging_list" style="margin-top: 10px;">
                    <?php
                    $display_list = new WPvivid_Staging_List_Ex();
                    $display_list->set_parent('wpvivid_staging_list');
                    $display_list->set_list($list);
                    $display_list->prepare_items();
                    $display_list->display();
                    $display_list->display_js();
                    ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
            if(is_multisite())
            {
                ?>
                <div id="wpvividstg_select_mu_staging_site" style="width: 100%; display:none;">

                </div>
                <?php
            }
            ?>

            <div id="wpvivid_custom_staging_site" style="display:none;">
                <?php
                $custom_staging_list = new WPvivid_Custom_Staging_List_Ex();
                $custom_staging_list ->set_parent_id('wpvivid_custom_staging_site');
                $custom_staging_list ->set_staging_home_path(true);
                $custom_staging_list ->display_rows();
                $custom_staging_list ->load_js();
                ?>
            </div>
        </div>
        <script>
            var push_staging_site_id='';
            var wpvivid_ajax_lock=false;

            function wpvivid_create_standard_json(){
                var json = {};
                json['core_list'] = Array();
                json['core_check'] = '0';
                json['database_list'] = Array();
                json['database_check'] = '1';
                json['themes_list'] = Array();
                json['plugins_list'] = Array();
                json['themes_check'] = '0';
                json['plugins_check'] = '0';
                json['uploads_list'] = {};
                json['uploads_check'] = '1';
                json['upload_extension']= Array();
                json['content_list'] = {};
                json['content_check'] = '0';
                json['content_extension']= Array();
                json['additional_file_list'] = {};
                json['additional_file_check'] = '0';
                json['additional_file_extension']= Array();
                return json;
            }

            function wpvivid_push_start_staging(mu_single)
            {
                var push_type = 'push_standard';
                var push_mu_site=false;
                jQuery('#'+push_staging_site_id).find('input:radio').each(function()
                {
                    if(jQuery(this).prop('checked')){
                        push_type = jQuery(this).attr('value');
                    }
                });
                if(push_type === 'push_standard')
                {
                    var custom_dir_json = wpvivid_create_standard_json();
                    var custom_dir = JSON.stringify(custom_dir_json);
                }
                else if(push_type === 'push_mu_site')
                {
                    var check_select = false;
                    jQuery('#wpvivid_mu_copy_staging_site_list').find('input:checkbox[name=copy_mu_site]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_select = true;
                        }
                    });

                    if(jQuery('input:checkbox[option=wpvividstg_copy_mu_sites][name=mu_all_site]').prop('checked')){
                        check_select = true;
                    }

                    if(jQuery('#wpvivid_staging_list').find('#wpvivid_mu_main_site_check').prop('checked')){
                        check_select = true;
                    }

                    if(mu_single){
                        check_select = true;
                    }

                    if(!check_select){
                        alert('Please select at least one item.');
                        return;
                    }

                    push_mu_site=true;
                    var json = {};
                    json['mu_site_list']=Array();
                    if(jQuery('input:checkbox[name=copy_mu_site_main]').prop('checked'))
                    {
                        var subjson = {};
                        subjson['check']=1;
                        subjson['id']=jQuery('input:checkbox[name=copy_mu_site_main]').val();
                        if(jQuery('input:checkbox[name=copy_mu_site_main_tables]').prop('checked'))
                        {
                            subjson['tables']=1;
                        }
                        else
                        {
                            subjson['tables']=0;
                        }

                        if(jQuery('input:checkbox[name=copy_mu_site_main_folders]').prop('checked'))
                        {
                            subjson['uploads_list'] = {};
                            jQuery('#wpvividstg_select_mu_staging_site').find('.wpvivid-custom-exclude-uploads-list ul').find('li div:eq(1)').each(function(){
                                var folder_name = this.innerHTML;
                                subjson['uploads_list'][folder_name] = {};
                                subjson['uploads_list'][folder_name]['name'] = folder_name;
                                subjson['uploads_list'][folder_name]['type'] = jQuery(this).prev().get(0).classList.item(0);
                            });
                            subjson['upload_extension'] = jQuery('#wpvividstg_select_mu_staging_site').find('.wpvivid-uploads-extension').val();
                            subjson['upload']=1;
                        }
                        else
                        {
                            subjson['upload']=0;
                            subjson['uploads_list'] = {};
                        }

                        if(jQuery('input:checkbox[name=copy_mu_site_main_core]').prop('checked'))
                        {
                            subjson['core']=1;
                        }
                        else
                        {
                            subjson['core']=0;
                        }

                        if(jQuery('input:checkbox[name=copy_mu_site_main_themes_plugins]').prop('checked'))
                        {
                            subjson['themes_plugins']=1;
                            subjson['themes_list'] = Array();
                            subjson['plugins_list'] = Array();
                            subjson['themes_check'] = '0';
                            subjson['plugins_check'] = '0';
                            jQuery('#wpvividstg_select_mu_staging_site').find('input:checkbox[option=themes][name=Themes]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    subjson['themes_check'] = '1';
                                }
                                else{
                                    subjson['themes_list'].push(jQuery(this).val());
                                }
                            });
                            jQuery('#wpvividstg_select_mu_staging_site').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                                if(jQuery(this).prop('checked')) {
                                    subjson['plugins_check'] = '1';
                                }
                                else{
                                    subjson['plugins_list'].push(jQuery(this).val());
                                }
                            });
                        }
                        else
                        {
                            subjson['themes_plugins']=0;
                            subjson['themes_check'] = '0';
                            subjson['plugins_check'] = '0';
                            subjson['themes_list'] = Array();
                            subjson['plugins_list'] = Array();
                        }

                        if(jQuery('input:checkbox[name=copy_mu_site_main_content]').prop('checked'))
                        {
                            subjson['content_list'] = {};
                            jQuery('#wpvividstg_select_mu_staging_site').find('.wpvivid-custom-exclude-content-list ul').find('li div:eq(1)').each(function(){
                                var folder_name = this.innerHTML;
                                subjson['content_list'][folder_name] = {};
                                subjson['content_list'][folder_name]['name'] = folder_name;
                                subjson['content_list'][folder_name]['type'] = jQuery(this).prev().get(0).classList.item(0);
                            });
                            subjson['content_extension'] = jQuery('#wpvividstg_select_mu_staging_site').find('.wpvivid-content-extension').val();
                            subjson['wp_content']=1;
                        }
                        else
                        {
                            subjson['wp_content']=0;
                            subjson['content_list'] = {};
                        }

                        if(jQuery('input:checkbox[name=copy_mu_site_main_additional_file]').prop('checked'))
                        {
                            subjson['additional_file_list'] = {};
                            subjson['additional_file'] = '1';
                            jQuery('#wpvividstg_select_mu_staging_site').find('.wpvivid-custom-include-additional-file-list ul').find('li div:eq(1)').each(function(){
                                var folder_name = this.innerHTML;
                                subjson['additional_file_list'][folder_name] = {};
                                subjson['additional_file_list'][folder_name]['name'] = folder_name;
                                subjson['additional_file_list'][folder_name]['type'] = jQuery(this).prev().get(0).classList.item(0);
                            });
                            subjson['additional_file_extension'] = jQuery('#wpvividstg_select_mu_staging_site').find('.wpvivid-additional-file-extension').val();
                        }
                        else
                        {
                            subjson['additional_file'] = '0';
                            subjson['additional_file_list'] = {};
                        }

                        json['mu_main_site']=subjson;
                    }
                    else
                    {
                        var subjson = {};
                        subjson['check']=0;
                        subjson['id']=jQuery('input:checkbox[name=copy_mu_site_main]').val();
                        json['mu_main_site']=subjson;
                    }

                    jQuery('input[name=copy_mu_site][type=checkbox]').each(function(index, value)
                    {
                        if(jQuery(value).prop('checked'))
                        {
                            var subjson = {};
                            subjson['id']=jQuery(value).val();
                            if(jQuery('input:checkbox[name=copy_mu_site_tables][value='+jQuery(value).val()+']').prop('checked'))
                            {
                                subjson['tables']=1;
                            }
                            else
                            {
                                subjson['tables']=0;
                            }
                            if(jQuery('input:checkbox[name=copy_mu_site_folders][value='+jQuery(value).val()+']').prop('checked'))
                            {
                                subjson['folders']=1;
                            }
                            else
                            {
                                subjson['folders']=0;
                            }
                            json['mu_site_list'].push(subjson);
                        }
                    });

                    if(jQuery('input:checkbox[option=wpvividstg_copy_mu_sites][name=mu_all_site]').prop('checked'))
                    {
                        json['all_site']=1;
                    }
                    else
                    {
                        json['all_site']=0;
                    }

                    var custom_dir = JSON.stringify(json);
                    jQuery('#wpvividstg_select_mu_staging_site').hide();
                }
                else if(push_type === 'push_custom')
                {
                    var custom_dir_json = wpvivid_create_custom_json(push_staging_site_id);
                    var custom_dir = JSON.stringify(custom_dir_json);
                    var check_status = wpvivid_check_staging_additional_folder_valid(push_staging_site_id);
                    if(!check_status) {
                        return;
                    }
                }
                else if(push_type === 'update_standard')
                {
                    var custom_dir_json = wpvivid_create_standard_json();
                    var custom_dir = JSON.stringify(custom_dir_json);
                }
                else if(push_type === 'update_mu_site')
                {
                    var check_select = false;
                    jQuery('#wpvivid_mu_copy_staging_site_list').find('input:checkbox[name=copy_mu_site]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_select = true;
                        }
                    });

                    if(jQuery('input:checkbox[option=wpvividstg_copy_mu_sites][name=mu_all_site]').prop('checked')){
                        check_select = true;
                    }

                    if(jQuery('#wpvivid_staging_list').find('#wpvivid_mu_main_site_check').prop('checked')){
                        check_select = true;
                    }

                    if(mu_single){
                        check_select = true;
                    }

                    if(!check_select){
                        alert('Please select at least one item.');
                        return;
                    }

                    push_mu_site=true;
                    var json = {};
                    json['mu_site_list']=Array();
                    if(jQuery('input:checkbox[name=copy_mu_site_main]').prop('checked'))
                    {
                        var subjson = {};
                        subjson['check']=1;
                        subjson['id']=jQuery('input:checkbox[name=copy_mu_site_main]').val();
                        if(jQuery('input:checkbox[name=copy_mu_site_main_tables]').prop('checked'))
                        {
                            subjson['tables']=1;
                        }
                        else
                        {
                            subjson['tables']=0;
                        }

                        if(jQuery('input:checkbox[name=copy_mu_site_main_folders]').prop('checked'))
                        {
                            subjson['uploads_list'] = {};
                            jQuery('#wpvividstg_select_mu_staging_site').find('.wpvivid-custom-exclude-uploads-list ul').find('li div:eq(1)').each(function(){
                                var folder_name = this.innerHTML;
                                subjson['uploads_list'][folder_name] = {};
                                subjson['uploads_list'][folder_name]['name'] = folder_name;
                                subjson['uploads_list'][folder_name]['type'] = jQuery(this).prev().get(0).classList.item(0);
                            });
                            subjson['upload_extension'] = jQuery('#wpvividstg_select_mu_staging_site').find('.wpvivid-uploads-extension').val();
                            subjson['upload']=1;
                        }
                        else
                        {
                            subjson['upload']=0;
                            subjson['uploads_list'] = {};
                        }

                        if(jQuery('input:checkbox[name=copy_mu_site_main_core]').prop('checked'))
                        {
                            subjson['core']=1;
                        }
                        else
                        {
                            subjson['core']=0;
                        }

                        if(jQuery('input:checkbox[name=copy_mu_site_main_themes_plugins]').prop('checked'))
                        {
                            subjson['themes_plugins']=1;
                            subjson['themes_list'] = Array();
                            subjson['plugins_list'] = Array();
                            subjson['themes_check'] = '0';
                            subjson['plugins_check'] = '0';
                            jQuery('#wpvividstg_select_mu_staging_site').find('input:checkbox[option=themes][name=Themes]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    subjson['themes_check'] = '1';
                                }
                                else{
                                    subjson['themes_list'].push(jQuery(this).val());
                                }
                            });
                            jQuery('#wpvividstg_select_mu_staging_site').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                                if(jQuery(this).prop('checked')) {
                                    subjson['plugins_check'] = '1';
                                }
                                else{
                                    subjson['plugins_list'].push(jQuery(this).val());
                                }
                            });
                        }
                        else
                        {
                            subjson['themes_plugins']=0;
                            subjson['themes_check'] = '0';
                            subjson['plugins_check'] = '0';
                            subjson['themes_list'] = Array();
                            subjson['plugins_list'] = Array();
                        }

                        if(jQuery('input:checkbox[name=copy_mu_site_main_content]').prop('checked'))
                        {
                            subjson['content_list'] = {};
                            jQuery('#wpvividstg_select_mu_staging_site').find('.wpvivid-custom-exclude-content-list ul').find('li div:eq(1)').each(function(){
                                var folder_name = this.innerHTML;
                                subjson['content_list'][folder_name] = {};
                                subjson['content_list'][folder_name]['name'] = folder_name;
                                subjson['content_list'][folder_name]['type'] = jQuery(this).prev().get(0).classList.item(0);
                            });
                            subjson['content_extension'] = jQuery('#wpvividstg_select_mu_staging_site').find('.wpvivid-content-extension').val();
                            subjson['wp_content']=1;
                        }
                        else
                        {
                            subjson['wp_content']=0;
                            subjson['content_list'] = {};
                        }

                        if(jQuery('input:checkbox[name=copy_mu_site_main_additional_file]').prop('checked'))
                        {
                            subjson['additional_file_list'] = {};
                            subjson['additional_file'] = '1';
                            jQuery('#wpvividstg_select_mu_staging_site').find('.wpvivid-custom-include-additional-file-list ul').find('li div:eq(1)').each(function(){
                                var folder_name = this.innerHTML;
                                subjson['additional_file_list'][folder_name] = {};
                                subjson['additional_file_list'][folder_name]['name'] = folder_name;
                                subjson['additional_file_list'][folder_name]['type'] = jQuery(this).prev().get(0).classList.item(0);
                            });
                            subjson['additional_file_extension'] = jQuery('#wpvividstg_select_mu_staging_site').find('.wpvivid-additional-file-extension').val();
                        }
                        else
                        {
                            subjson['additional_file'] = '0';
                            subjson['additional_file_list'] = {};
                        }

                        json['mu_main_site']=subjson;
                    }
                    else
                    {
                        var subjson = {};
                        subjson['check']=0;
                        subjson['id']=jQuery('input:checkbox[name=copy_mu_site_main]').val();
                        json['mu_main_site']=subjson;
                    }

                    jQuery('input[name=copy_mu_site][type=checkbox]').each(function(index, value)
                    {
                        if(jQuery(value).prop('checked'))
                        {
                            var subjson = {};
                            subjson['id']=jQuery(value).val();
                            if(jQuery('input:checkbox[name=copy_mu_site_tables][value='+jQuery(value).val()+']').prop('checked'))
                            {
                                subjson['tables']=1;
                            }
                            else
                            {
                                subjson['tables']=0;
                            }
                            if(jQuery('input:checkbox[name=copy_mu_site_folders][value='+jQuery(value).val()+']').prop('checked'))
                            {
                                subjson['folders']=1;
                            }
                            else
                            {
                                subjson['folders']=0;
                            }
                            json['mu_site_list'].push(subjson);
                        }
                    });

                    if(jQuery('input:checkbox[option=wpvividstg_copy_mu_sites][name=mu_all_site]').prop('checked'))
                    {
                        json['all_site']=1;
                    }
                    else
                    {
                        json['all_site']=0;
                    }

                    var custom_dir = JSON.stringify(json);
                    jQuery('#wpvividstg_select_mu_staging_site').hide();
                }
                else if(push_type === 'update_custom')
                {
                    var custom_dir_json = wpvivid_create_custom_json(push_staging_site_id);
                    var custom_dir = JSON.stringify(custom_dir_json);
                    var check_status = wpvivid_check_staging_additional_folder_valid(push_staging_site_id);
                    if(!check_status) {
                        return;
                    }
                }

                var action='wpvividstg_push_start_staging';
                if(push_type === 'push_standard'||push_type === 'push_custom'||push_type === 'push_mu_site')
                {
                    action='wpvividstg_push_start_staging';
                }
                else if(push_type === 'update_standard'||push_type === 'update_custom'||push_type === 'update_mu_site')
                {
                    action='wpvividstg_copy_start_staging';
                }

                var ajax_data = {
                    'action':action,
                    'wpvivid_restore' : '1',
                    'id': push_staging_site_id,
                    'push_mu_site':push_mu_site,
                    'custom_dir': custom_dir
                };

                jQuery('#'+push_staging_site_id).find('.wpvivid-push-content').html('<div class="postbox wpvivid-staging-log" id="wpvivid_push_staging_log" style="margin-bottom: 0;"></div>');
                wpvivid_lock_unlock_push_ui('lock');
                wpvivid_post_request(ajax_data, function(data)
                {
                    jQuery('#wpvivid_custom_staging_site').hide();
                    jQuery('#wpvividstg_select_mu_staging_site').hide();
                    setTimeout(function()
                    {
                        if(action=='wpvividstg_push_start_staging')
                            wpvivid_get_push_staging_progress();
                        else
                            wpvivid_get_copy_staging_progress();
                    }, staging_requet_timeout);
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    setTimeout(function()
                    {
                        if(action=='wpvividstg_push_start_staging')
                            wpvivid_get_push_staging_progress();
                        else
                            wpvivid_get_copy_staging_progress();
                    }, staging_requet_timeout);
                });
            }

            function wpvivid_get_copy_staging_progress()
            {
                if(wpvivid_ajax_lock)
                    return ;
                var ajax_data = {
                    'action':'wpvividstg_get_staging_progress'
                };
                wpvivid_ajax_lock=true;
                wpvivid_post_request(ajax_data, function(data)
                {
                    try
                    {
                        wpvivid_ajax_lock=false;
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            var log_data = jsonarray.log;
                            jQuery('#wpvivid_push_staging_log').html("");
                            while (log_data.indexOf('\n') >= 0)
                            {
                                var iLength = log_data.indexOf('\n');
                                var log = log_data.substring(0, iLength);
                                log_data = log_data.substring(iLength + 1);
                                var insert_log = "<div style=\"clear:both;\">" + log + "</div>";
                                jQuery('#wpvivid_push_staging_log').append(insert_log);
                                var div = jQuery('#wpvivid_push_staging_log');
                                div[0].scrollTop = div[0].scrollHeight;
                            }
                            if(jsonarray.continue)
                            {
                                if(jsonarray.need_restart)
                                {
                                    wpvivid_copy_restart_staging();
                                }
                                else
                                {
                                    setTimeout(function()
                                    {
                                        wpvivid_get_copy_staging_progress();
                                    }, staging_requet_timeout);
                                }
                            }
                            else{
                                wpvivid_lock_unlock_push_ui('unlock');
                                if(typeof jsonarray.completed !== 'undefined' && jsonarray.completed){
                                    alert(jsonarray.completed_msg);
                                    //alert('Pushing the staging site to the live site completed successfully.');
                                    //alert("Updating the staging site completed successfully.");
                                    location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=WPvivid_Staging'; ?>';
                                }
                                else if(typeof jsonarray.error !== 'undefined' && jsonarray.error){
                                    wpvivid_create_staging_lock_unlock('unlock');
                                }
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            wpvivid_lock_unlock_push_ui('unlock');
                            alert(jsonarray.error);
                        }
                    }
                    catch(err)
                    {
                        setTimeout(function()
                        {
                            wpvivid_get_copy_staging_progress();
                        }, 3000);
                    }

                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    wpvivid_ajax_lock=false;
                    setTimeout(function()
                    {
                        wpvivid_get_copy_staging_progress();
                    }, 3000);
                });
            }

            function wpvivid_push_restart_staging()
            {
                var ajax_data = {
                    'action':'wpvividstg_push_restart_staging',
                    'wpvivid_restore' : '1'
                };

                wpvivid_post_request(ajax_data, function(data)
                {
                    setTimeout(function()
                    {
                        wpvivid_get_copy_staging_progress();
                    }, staging_requet_timeout);
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    setTimeout(function()
                    {
                        wpvivid_get_copy_staging_progress();
                    }, staging_requet_timeout);
                });
            }

            function wpvivid_copy_restart_staging()
            {
                var ajax_data = {
                    'action':'wpvividstg_copy_restart_staging',
                };

                wpvivid_post_request(ajax_data, function(data)
                {
                    setTimeout(function()
                    {
                        wpvivid_get_push_staging_progress();
                    }, staging_requet_timeout);
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    setTimeout(function()
                    {
                        wpvivid_get_push_staging_progress();
                    }, staging_requet_timeout);
                });
            }

            function wpvivid_get_push_staging_progress()
            {
                if(wpvivid_ajax_lock)
                    return ;
                var ajax_data = {
                    'action':'wpvividstg_get_staging_progress',
                    'wpvivid_restore' : '1'
                };
                wpvivid_ajax_lock=true;
                wpvivid_post_request(ajax_data, function(data)
                {
                    try
                    {
                        wpvivid_ajax_lock=false;
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            var log_data = jsonarray.log;
                            jQuery('#wpvivid_push_staging_log').html("");
                            while (log_data.indexOf('\n') >= 0)
                            {
                                var iLength = log_data.indexOf('\n');
                                var log = log_data.substring(0, iLength);
                                log_data = log_data.substring(iLength + 1);
                                var insert_log = "<div style=\"clear:both;\">" + log + "</div>";
                                jQuery('#wpvivid_push_staging_log').append(insert_log);
                                var div = jQuery('#wpvivid_push_staging_log');
                                div[0].scrollTop = div[0].scrollHeight;
                            }
                            if(jsonarray.continue)
                            {
                                if(jsonarray.need_restart)
                                {
                                    wpvivid_push_restart_staging();
                                }
                                else
                                {
                                    setTimeout(function()
                                    {
                                        wpvivid_get_push_staging_progress();
                                    }, staging_requet_timeout);
                                }
                            }
                            else{
                                wpvivid_lock_unlock_push_ui('unlock');
                                if(typeof jsonarray.completed !== 'undefined' && jsonarray.completed){
                                    alert(jsonarray.completed_msg);
                                    //alert("Pushing the staging site to the live site completed successfully.");
                                    location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=WPvivid_Staging'; ?>';
                                }
                                else if(typeof jsonarray.error !== 'undefined' && jsonarray.error){
                                    wpvivid_create_staging_lock_unlock('unlock');
                                }
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            wpvivid_lock_unlock_push_ui('unlock');
                            alert(jsonarray.error);
                        }
                    }
                    catch(err)
                    {
                        setTimeout(function()
                        {
                            wpvivid_get_push_staging_progress();
                        }, 3000);
                    }

                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    wpvivid_ajax_lock=false;
                    setTimeout(function()
                    {
                        wpvivid_get_push_staging_progress();
                    }, 3000);
                });
            }

            var staging_uploads_path = '';
            var staging_content_path = '';
            var staging_home_path = '';
            function wpvivid_load_staging_tree(parent_id, is_staging){
                jQuery('#' + parent_id).on("click", '.wpvivid-handle-uploads-detail', function () {
                    wpvivid_init_custom_tree('uploads', parent_id, is_staging, staging_uploads_path, 0);
                });

                jQuery('#' + parent_id).on("click", '.wpvivid-handle-content-detail', function () {
                    wpvivid_init_custom_tree('content', parent_id, is_staging, staging_content_path, 0);
                });

                jQuery('#' + parent_id).on("click", '.wpvivid-handle-additional-file-detail', function () {
                    wpvivid_init_custom_tree('additional_file', parent_id, is_staging, staging_home_path, 0);
                });

                jQuery('#' + parent_id).on("click", '.wpvivid-refresh-tree', function () {
                    if (jQuery(this).hasClass('wpvivid-refresh-uploads-tree')) {
                        wpvivid_init_custom_tree('uploads', parent_id, is_staging, staging_uploads_path, 1);
                    }
                    else if (jQuery(this).hasClass('wpvivid-refresh-content-tree')) {
                        wpvivid_init_custom_tree('content', parent_id, is_staging, staging_content_path, 1);
                    }
                    else if (jQuery(this).hasClass('wpvivid-refresh-additional-file-tree')) {
                        wpvivid_init_custom_tree('additional_file', parent_id, is_staging, staging_home_path, 1);
                    }
                });

                jQuery('#' + parent_id).on("click", '.wpvivid-exclude-uploads-folder-btn', function () {
                    wpvivid_include_exclude_folder('uploads', parent_id, staging_uploads_path);
                });

                jQuery('#' + parent_id).on("click", '.wpvivid-exclude-content-folder-btn', function () {
                    wpvivid_include_exclude_folder('content', parent_id, staging_content_path);
                });

                jQuery('#' + parent_id).on("click", '.wpvivid-include-additional-file-btn', function () {
                    wpvivid_include_exclude_folder('additional_file', parent_id, staging_home_path);
                });

                jQuery('#' + parent_id).on("click", '.wpvivid-custom-uploads-tree-info', function () {
                    var tree_path = staging_uploads_path;
                    var select_path = jQuery(this).jstree(true).get_selected();
                    if (select_path == tree_path) {
                        alert("The root directory is not allowed to select.");
                    }
                });

                jQuery('#' + parent_id).on("click", '.wpvivid-custom-content-tree-info', function () {
                    var tree_path = staging_content_path;
                    var select_path = jQuery(this).jstree(true).get_selected();
                    if (select_path == tree_path) {
                        alert("The root directory is not allowed to select.");
                    }
                });

                jQuery('#' + parent_id).on("click", '.wpvivid-custom-additional-file-tree-info', function () {
                    var tree_path = staging_home_path;
                    var select_path = jQuery(this).jstree(true).get_selected();
                    if (select_path == tree_path) {
                        alert("The root directory is not allowed to select.");
                    }
                });
            }
            wpvivid_load_staging_tree('wpvivid_custom_staging_site', true);

            function wpvivid_staging_js_fix(parent_id, is_staging, uploads_path, content_path, home_path, staging_site_id){
                staging_uploads_path = uploads_path;
                staging_content_path = content_path;
                staging_home_path = home_path;

                jQuery('#'+parent_id).find('.wpvivid-custom-uploads-tree-info').jstree("destroy").empty();
                jQuery('#'+parent_id).find('.wpvivid-custom-content-tree-info').jstree("destroy").empty();
                jQuery('#'+parent_id).find('.wpvivid-custom-additional-file-tree-info').jstree("destroy").empty();

                if(is_staging){
                    is_staging = '1';
                }
                else{
                    is_staging = '0';
                }
                wpvivid_get_custom_database_tables_info(parent_id, is_staging, staging_site_id);
                wpvivid_get_custom_themes_plugins_info(parent_id, is_staging, staging_site_id, home_path);
            }

            function wpvivid_lock_unlock_push_ui(action){
                if(action === 'lock'){
                    jQuery('#wpvivid_staging_list').find('a').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_staging_list').find('input').attr('disabled', true);
                    jQuery('#wpvivid_staging_list').find('div.wpvivid-delete-staging-site').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_staging_list').find('div#wpvivid_custom_staging_site').css({'pointer-events': 'none', 'opacity': '0.4'});
                }
                else{
                    jQuery('#wpvivid_staging_list').find('a').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_staging_list').find('input').attr('disabled', false);
                    jQuery('#wpvivid_staging_list').find('div.wpvivid-delete-staging-site').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_staging_list').find('div#wpvivid_custom_staging_site').css({'pointer-events': 'auto', 'opacity': '1'});
                }
            }

            function wpvivid_get_mu_site_info(id,copy)
            {
                var ajax_data = {
                    'action':'wpvividstg_get_mu_site_info',
                    'id': id,
                    'copy':copy
                };
                wpvivid_lock_unlock_push_ui('lock');
                wpvivid_post_request(ajax_data, function(data)
                {
                    wpvivid_lock_unlock_push_ui('unlock');
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        push_staging_site_id=id;
                        jQuery('#wpvividstg_select_mu_staging_site').html(jsonarray.html);
                        jQuery('#'+id).find('.wpvivid-push-content').after(jQuery('#wpvividstg_select_mu_staging_site'));
                        jQuery('#wpvividstg_select_mu_staging_site').show();
                        wpvivid_load_mu_staging_js('wpvivid_custom_mu_staging_site');
                        if(copy == 'true' || copy == true){
                            wpvivid_load_staging_tree('wpvivid_custom_mu_staging_site', true);
                            wpvivid_staging_js_fix('wpvivid_custom_mu_staging_site', true, jsonarray.uploads_path, jsonarray.content_path, jsonarray.home_path, id);
                        }
                        else{
                            wpvivid_load_staging_tree('wpvivid_custom_mu_staging_site', false);
                            wpvivid_staging_js_fix('wpvivid_custom_mu_staging_site', false, jsonarray.uploads_path, jsonarray.content_path, jsonarray.home_path, id);
                        }
                        jQuery('#wpvivid_mu_copy_staging_site_list').find('input:checkbox').each(function(){
                            jQuery(this).prop('checked', true);
                        });
                    }
                    else if (jsonarray.result === 'failed')
                    {
                        alert(jsonarray.error);
                    }

                    jQuery('#wpvivid_staging_list').find('.wpvivid-copy-staging-to-live-block').each(function()
                    {
                        var tmp_id = jQuery(this).parents('tr').attr('id');
                        if(id !== tmp_id) {
                            if(jQuery(this).hasClass('staging-site')){
                                var class_btn = 'staging-site';
                                var copy_btn = 'Copy the Staging Site to Live';
                                var update_btn = 'Update the Staging Site';
                                var tip_text = 'Tips: Click the \'Copy the Staging Site to Live\' button above to migrate the staging site to your live site. Click the \'Update the Staging Site\' button to update the live site to the staging site.';
                            }
                            else{
                                var class_btn = 'fresh-install';
                                var copy_btn = 'Copy the Fresh Install to Live';
                                var update_btn = 'Update the Fresh Install';
                                var tip_text = 'Tips: Click the \'Copy the Fresh Install to Live\' button above to migrate the fresh install to your live site. Click the \'Update the Fresh Install\' button to update the live site to the fresh install.';
                            }

                            if(jQuery(this).hasClass('mu-single')){
                                var mu_single_class = 'mu-single';
                            }
                            else{
                                var mu_single_class = '';
                            }

                            var tmp_html = '<div>' +
                                '<input class="button-primary wpvivid-copy-staging-to-live '+class_btn+' '+mu_single_class+'" type="button" value="'+copy_btn+'" style="margin-right: 10px;" />' +
                                '<input class="button-primary wpvivid-update-live-to-staging '+class_btn+' '+mu_single_class+'" type="button" value="'+update_btn+'" /></div>' +
                                '<div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>'+tip_text+'</span></div>';
                            jQuery(this).html(tmp_html);
                        }
                    });
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    wpvivid_lock_unlock_push_ui('unlock');
                    var error_message = wpvivid_output_ajaxerror('export the previously-exported settings', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function wpvivid_load_mu_staging_js(parent_id)
            {
                function wpvivid_handle_custom_open_close(obj, sub_obj){
                    if(obj.hasClass('wpvivid-close')) {
                        sub_obj.hide();
                        sub_obj.prev().find('details').prop('open', false);
                        sub_obj.removeClass('wpvivid-open');
                        sub_obj.addClass('wpvivid-close');
                        sub_obj.prev().css('background-color', '#fff');
                        obj.prev().css('background-color', '#f1f1f1');
                        obj.prev().find('details').prop('open', true);
                        obj.show();
                        obj.removeClass('wpvivid-close');
                        obj.addClass('wpvivid-open');
                    }
                    else{
                        obj.hide();
                        obj.prev().css('background-color', '#fff');
                        obj.prev().find('details').prop('open', false);
                        obj.removeClass('wpvivid-open');
                        obj.addClass('wpvivid-close');
                    }
                }

                jQuery('#'+parent_id).on("click", '.wpvivid-handle-database-detail', function() {
                    var obj = jQuery('#'+parent_id).find('.wpvivid-database-detail');
                    var sub_obj = jQuery('#'+parent_id).find('.wpvivid-custom-detail');
                    wpvivid_handle_custom_open_close(obj, sub_obj);
                });

                jQuery('#'+parent_id).on("click", '.wpvivid-handle-themes-plugins-detail', function() {
                    var obj = jQuery('#'+parent_id).find('.wpvivid-themes-plugins-detail');
                    var sub_obj = jQuery('#'+parent_id).find('.wpvivid-custom-detail');
                    wpvivid_handle_custom_open_close(obj, sub_obj);
                });

                jQuery('#'+parent_id).on("click", '.wpvivid-handle-uploads-detail', function() {
                    var obj = jQuery('#'+parent_id).find('.wpvivid-uploads-detail');
                    var sub_obj = jQuery('#'+parent_id).find('.wpvivid-custom-detail');
                    wpvivid_handle_custom_open_close(obj, sub_obj);
                });

                jQuery('#'+parent_id).on("click", '.wpvivid-handle-content-detail', function() {
                    var obj = jQuery('#'+parent_id).find('.wpvivid-content-detail');
                    var sub_obj = jQuery('#'+parent_id).find('.wpvivid-custom-detail');
                    wpvivid_handle_custom_open_close(obj, sub_obj);
                });

                jQuery('#'+parent_id).on("click", '.wpvivid-handle-additional-file-detail', function() {
                    var obj = jQuery('#'+parent_id).find('.wpvivid-additional-file-detail');
                    var sub_obj = jQuery('#'+parent_id).find('.wpvivid-custom-detail');
                    wpvivid_handle_custom_open_close(obj, sub_obj);
                });

                jQuery('#'+parent_id).on("click", '.wpvivid-custom-check', function() {
                    if (jQuery(this).prop('checked')) {
                        if(!jQuery(this).hasClass('wpvivid-custom-core-check')) {
                            jQuery(jQuery(this).parents('tr').next().get(0)).css({'pointer-events': 'auto', 'opacity': '1'});
                        }
                    }
                    else{
                        var check_status = false;
                        jQuery('#'+parent_id).find('.wpvivid-custom-check').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            if (!jQuery(this).hasClass('wpvivid-custom-core-check')) {
                                jQuery(jQuery(this).parents('tr').next().get(0)).css({'pointer-events': 'none', 'opacity': '0.4'});
                            }
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one item under Custom option.');
                        }
                    }
                });

                jQuery('#'+parent_id).on("click", '.wpvivid-database-table-check', function() {
                    if(jQuery(this).prop('checked')){
                        if(jQuery(this).hasClass('wpvivid-database-base-table-check')){
                            jQuery('#'+parent_id).find('input:checkbox[option=base_db][name=Database]').prop('checked', true);
                        }
                        else if(jQuery(this).hasClass('wpvivid-database-woo-table-check')){
                            jQuery('#'+parent_id).find('input:checkbox[option=woo_db][name=Database]').prop('checked', true);
                        }
                        else if(jQuery(this).hasClass('wpvivid-database-other-table-check')){
                            jQuery('#'+parent_id).find('input:checkbox[option=other_db][name=Database]').prop('checked', true);
                        }
                    }
                    else{
                        var check_status = false;
                        if (jQuery(this).hasClass('wpvivid-database-base-table-check')) {
                            jQuery('#'+parent_id).find('input:checkbox[option=other_db][name=Database]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                            jQuery('#'+parent_id).find('input:checkbox[option=woo_db][name=Database]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                            if(check_status) {
                                jQuery('#'+parent_id).find('input:checkbox[option=base_db][name=Database]').prop('checked', false);
                            }
                            else{
                                jQuery(this).prop('checked', true);
                                alert('Please select at least one table type under the Database option, or deselect the option.');
                            }
                        }
                        else if (jQuery(this).hasClass('wpvivid-database-woo-table-check')) {
                            jQuery('#'+parent_id).find('input:checkbox[option=base_db][name=Database]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                            jQuery('#'+parent_id).find('input:checkbox[option=other_db][name=Database]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                            if(check_status) {
                                jQuery('#'+parent_id).find('input:checkbox[option=woo_db][name=Database]').prop('checked', false);
                            }
                            else{
                                jQuery(this).prop('checked', true);
                                alert('Please select at least one table type under the Database option, or deselect the option.');
                            }
                        }
                        else if (jQuery(this).hasClass('wpvivid-database-other-table-check')) {
                            jQuery('#'+parent_id).find('input:checkbox[option=base_db][name=Database]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                            jQuery('#'+parent_id).find('input:checkbox[option=woo_db][name=Database]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                            if(check_status) {
                                jQuery('#'+parent_id).find('input:checkbox[option=other_db][name=Database]').prop('checked', false);
                            }
                            else{
                                jQuery(this).prop('checked', true);
                                alert('Please select at least one table type under the Database option, or deselect the option.');
                            }
                        }
                    }
                });

                jQuery('#'+parent_id).on("click", 'input:checkbox[option=base_db][name=Database]', function(){
                    if(jQuery(this).prop('checked')){
                        var all_check = true;
                        jQuery('#'+parent_id).find('input:checkbox[option=base_db][name=Database]').each(function(){
                            if(!jQuery(this).prop('checked')){
                                all_check = false;
                            }
                        });
                        if(all_check){
                            jQuery('#'+parent_id).find('.wpvivid-database-base-table-check').prop('checked', true);
                        }
                    }
                    else{
                        var check_status = false;
                        jQuery('#'+parent_id).find('input:checkbox[name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status){
                            jQuery('#'+parent_id).find('.wpvivid-database-base-table-check').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                });

                jQuery('#'+parent_id).on("click", 'input:checkbox[option=woo_db][name=Database]', function(){
                    if(jQuery(this).prop('checked')){
                        var all_check = true;
                        jQuery('#'+parent_id).find('input:checkbox[option=woo_db][name=Database]').each(function(){
                            if(!jQuery(this).prop('checked')){
                                all_check = false;
                            }
                        });
                        if(all_check){
                            jQuery('#'+parent_id).find('.wpvivid-database-woo-table-check').prop('checked', true);
                        }
                    }
                    else{
                        var check_status = false;
                        jQuery('#'+parent_id).find('input:checkbox[name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status){
                            jQuery('#'+parent_id).find('.wpvivid-database-woo-table-check').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                });

                jQuery('#'+parent_id).on("click", 'input:checkbox[option=other_db][name=Database]', function(){
                    if(jQuery(this).prop('checked')){
                        var all_check = true;
                        jQuery('#'+parent_id).find('input:checkbox[option=other_db][name=Database]').each(function(){
                            if(!jQuery(this).prop('checked')){
                                all_check = false;
                            }
                        });
                        if(all_check){
                            jQuery('#'+parent_id).find('.wpvivid-database-other-table-check').prop('checked', true);
                        }
                    }
                    else{
                        var check_status = false;
                        jQuery('#'+parent_id).find('input:checkbox[name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status){
                            jQuery('#'+parent_id).find('.wpvivid-database-other-table-check').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                });

                jQuery('#'+parent_id).on("click", '.wpvivid-themes-plugins-table-check', function(){
                    if(jQuery(this).prop('checked')){
                        if(jQuery(this).hasClass('wpvivid-themes-table-check')){
                            jQuery('#'+parent_id).find('input:checkbox[option=themes][name=Themes]').prop('checked', true);
                        }
                        else if(jQuery(this).hasClass('wpvivid-plugins-table-check')){
                            jQuery('#'+parent_id).find('input:checkbox[option=plugins][name=Plugins]').prop('checked', true);
                        }
                    }
                    else{
                        var check_status = false;
                        if (jQuery(this).hasClass('wpvivid-themes-table-check')) {
                            jQuery('#'+parent_id).find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                            if(check_status) {
                                jQuery('#'+parent_id).find('input:checkbox[option=themes][name=Themes]').prop('checked', false);
                            }
                            else{
                                jQuery(this).prop('checked', true);
                                alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                            }
                        }
                        else if (jQuery(this).hasClass('wpvivid-plugins-table-check')) {
                            jQuery('#'+parent_id).find('input:checkbox[option=themes][name=Themes]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                            if(check_status) {
                                jQuery('#'+parent_id).find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                                    if(jQuery(this).val() !== 'wpvivid-backuprestore' && jQuery(this).val() !== 'wpvivid-backup-pro'){
                                        jQuery(this).prop('checked', false);
                                    }
                                });
                            }
                            else{
                                jQuery(this).prop('checked', true);
                                alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                            }
                        }
                    }
                });

                jQuery('#'+parent_id).on("click", 'input:checkbox[option=themes][name=Themes]', function(){
                    if(jQuery(this).prop('checked')){
                        var all_check = true;
                        jQuery('#'+parent_id).find('input:checkbox[option=themes][name=Themes]').each(function(){
                            if(!jQuery(this).prop('checked')){
                                all_check = false;
                            }
                        });
                        if(all_check){
                            jQuery('#'+parent_id).find('.wpvivid-themes-table-check').prop('checked', true);
                        }
                    }
                    else{
                        var check_status = false;
                        jQuery('#'+parent_id).find('input:checkbox[option=themes][name=Themes]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(!check_status){
                            jQuery('#'+parent_id).find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                        }
                        if(check_status){
                            jQuery('#'+parent_id).find('.wpvivid-themes-table-check').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                        }
                    }
                });

                jQuery('#'+parent_id).on("click", 'input:checkbox[option=plugins][name=Plugins]', function(){
                    if(jQuery(this).prop('checked')){
                        var all_check = true;
                        jQuery('#'+parent_id).find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                            if(!jQuery(this).prop('checked')){
                                all_check = false;
                            }
                        });
                        if(all_check){
                            jQuery('#'+parent_id).find('.wpvivid-plugins-table-check').prop('checked', true);
                        }
                    }
                    else{
                        var check_status = false;
                        jQuery('#'+parent_id).find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(!check_status){
                            jQuery('#'+parent_id).find('input:checkbox[option=themes][name=Themes]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                        }
                        if(check_status){
                            jQuery('#'+parent_id).find('.wpvivid-plugins-table-check').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                        }
                    }
                });

                jQuery('#'+parent_id).on("click", '.wpvivid-uploads-extension-rule-btn', function(){
                    var value = jQuery(this).prev().val();
                    if(value!=='') {
                        wpvivid_update_staging_exclude_extension('upload', value);
                    }
                });

                jQuery('#'+parent_id).on("click", '.wpvivid-content-extension-rule-btn', function(){
                    var value = jQuery(this).prev().val();
                    if(value!=='') {
                        wpvivid_update_staging_exclude_extension('content', value);
                    }
                });

                jQuery('#'+parent_id).on("click", '.wpvivid-additional-file-extension-rule-btn', function(){
                    var value = jQuery(this).prev().val();
                    if(value!=='') {
                        wpvivid_update_staging_exclude_extension('additional_file', value);
                    }
                });

                function wpvivid_update_staging_exclude_extension(type, value){
                    var ajax_data = {
                        'action': 'wpvividstg_update_staging_exclude_extension',
                        'type': type,
                        'exclude_content': value
                    };
                    jQuery(this).css({'pointer-events': 'none', 'opacity': '0.4'});
                    wpvivid_post_request(ajax_data, function (data) {
                        jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                        try {
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray.result === 'success') {
                            }
                        }
                        catch (err) {
                            alert(err);
                        }
                    }, function (XMLHttpRequest, textStatus, errorThrown) {
                        jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                        var error_message = wpvivid_output_ajaxerror('saving staging extension', textStatus, errorThrown);
                        alert(error_message);
                    });
                }

                jQuery('#'+parent_id).on("click", '.wpvivid-custom-li-close', function(){
                    jQuery(this).parent().parent().remove();
                });
            }

            jQuery('#wpvivid_staging_list').on("click",'.first-page',function() {
                wpvivid_get_copy_mu_list('first');
            });

            jQuery('#wpvivid_staging_list').on("click",'.prev-page',function() {
                var page=parseInt(jQuery(this).attr('value'));
                wpvivid_get_copy_mu_list(page-1);
            });

            jQuery('#wpvivid_staging_list').on("click",'.next-page',function() {
                var page=parseInt(jQuery(this).attr('value'));
                wpvivid_get_copy_mu_list(page+1);
            });

            jQuery('#wpvivid_staging_list').on("click",'.last-page',function() {
                wpvivid_get_copy_mu_list('last');
            });

            jQuery('#wpvivid_staging_list').on("keypress", '.current-page', function(){
                if(event.keyCode === 13){
                    var page = jQuery(this).val();
                    wpvivid_get_copy_mu_list(page);
                }
            });

            function wpvivid_get_copy_mu_list(page)
            {
                var copy=false;
                if(page==0)
                {
                    page =jQuery('#wpvivid_mu_copy_staging_site_list').find('.current-page').val();
                }
                var push_type = 'push_standard';
                jQuery('#'+push_staging_site_id).find('input:radio').each(function()
                {
                    if(jQuery(this).prop('checked')){
                        push_type = jQuery(this).attr('value');
                    }
                });
                if(push_type === 'update_standard'||push_type === 'update_custom'||push_type === 'update_mu_site')
                {
                    copy=true;
                }

                var search = jQuery('#wpvivid-mu-site-copy-search-input').val();
                var ajax_data = {
                    'action': 'wpvivid_get_mu_list',
                    'search':search,
                    'copy':copy,
                    'id':push_staging_site_id,
                    'page':page
                };
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_mu_copy_staging_site_list').html('');
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_mu_copy_staging_site_list').html(jsonarray.html);
                        }
                        else
                        {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err)
                    {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('achieving backup', textStatus, errorThrown);
                    alert(error_message);
                });
            }
//
            jQuery('#wpvivid_staging_list').on("click",'#wpvivid-mu-copy-search-submit',function()
            {
                var copy=false;
                var push_type = 'push_standard';
                jQuery('#'+push_staging_site_id).find('input:radio').each(function()
                {
                    if(jQuery(this).prop('checked')){
                        push_type = jQuery(this).attr('value');
                    }
                });
                if(push_type === 'update_standard'||push_type === 'update_custom'||push_type === 'update_mu_site')
                {
                    copy=true;
                }
                var search = jQuery('#wpvivid-mu-site-copy-search-input').val();
                var ajax_data = {
                    'action': 'wpvivid_get_mu_list',
                    'copy':copy,
                    'id':push_staging_site_id,
                    'search':search
                };
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_mu_copy_staging_site_list').html('');
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_mu_copy_staging_site_list').html(jsonarray.html);
                        }
                        else
                        {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err)
                    {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('achieving backup', textStatus, errorThrown);
                    alert(error_message);
                });
            });

            jQuery('#wpvivid_staging_list').on("click",'#wpvivid_mu_main_site_check',function()
            {
                if(jQuery(this).prop('checked'))
                {
                    jQuery('#wpvivid_mu_main_site_check_table').show();
                }
                else
                {
                    jQuery('#wpvivid_mu_main_site_check_table').hide();
                }
            });

            jQuery('#wpvivid_staging_list').on("click",'input:checkbox[option=wpvividstg_copy_mu_sites][name=mu_all_site]',function()
            {
                if(jQuery('input:checkbox[option=wpvividstg_copy_mu_sites][name=mu_all_site]').prop('checked'))
                {
                    jQuery('#wpvivid_mu_copy_staging_site_list').find('input:checkbox').each(function(){
                        jQuery(this).prop('checked', true);
                    });
                    jQuery('#wpvivid_mu_copy_staging_site_list').css({'pointer-events': 'none', 'opacity': '0.4'});
                }
                else{
                    jQuery('#wpvivid_mu_copy_staging_site_list').find('input:checkbox').each(function(){
                        jQuery(this).prop('checked', false);
                    });
                    jQuery('#wpvivid_mu_copy_staging_site_list').css({'pointer-events': 'auto', 'opacity': '1'});
                }
            });

            function wpvivid_push_site(id)
            {
                var ajax_data = {
                    'action':'wpvividstg_push_site',
                    'id': id
                };
                wpvivid_lock_unlock_push_ui('lock');
                wpvivid_post_request(ajax_data, function(data)
                {
                    wpvivid_lock_unlock_push_ui('unlock');
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        push_staging_site_id=id;
                        wpvivid_staging_js_fix('wpvivid_custom_staging_site', true, jsonarray.uploads_path, jsonarray.content_path, jsonarray.home_path, id);
                        jQuery('#'+id).find('.wpvivid-push-content').after(jQuery('#wpvivid_custom_staging_site'));
                        jQuery('#wpvivid_custom_staging_site').show();

                        jQuery('#wpvivid_staging_list').find('.wpvivid-copy-staging-to-live-block').each(function()
                        {
                            var tmp_id = jQuery(this).parents('tr').attr('id');
                            if(id !== tmp_id) {
                                if(jQuery(this).hasClass('staging-site')){
                                    var class_btn = 'staging-site';
                                    var copy_btn = 'Copy the Staging Site to Live';
                                    var update_btn = 'Update the Staging Site';
                                    var tip_text = 'Tips: Click the \'Copy the Staging Site to Live\' button above to migrate the staging site to your live site. Click the \'Update the Staging Site\' button to update the live site to the staging site.';
                                }
                                else{
                                    var class_btn = 'fresh-install';
                                    var copy_btn = 'Copy the Fresh Install to Live';
                                    var update_btn = 'Update the Fresh Install';
                                    var tip_text = 'Tips: Click the \'Copy the Fresh Install to Live\' button above to migrate the fresh install to your live site. Click the \'Update the Fresh Install\' button to update the live site to the fresh install.';
                                }

                                if(jQuery(this).hasClass('mu-single')){
                                    var mu_single_class = 'mu-single';
                                }
                                else{
                                    var mu_single_class = '';
                                }

                                var tmp_html = '<div>' +
                                    '<input class="button-primary wpvivid-copy-staging-to-live '+class_btn+' '+mu_single_class+'" type="button" value="'+copy_btn+'" style="margin-right: 10px;" />' +
                                    '<input class="button-primary wpvivid-update-live-to-staging '+class_btn+' '+mu_single_class+'" type="button" value="'+update_btn+'" /></div>' +
                                    '<div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>'+tip_text+'</span></div>';
                                jQuery(this).html(tmp_html);
                            }
                        });
                    }
                    else if (jsonarray.result === 'failed')
                    {
                        alert(jsonarray.error);
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    wpvivid_lock_unlock_push_ui('unlock');
                    var error_message = wpvivid_output_ajaxerror('export the previously-exported settings', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function wpvivid_copy_site(id)
            {
                var ajax_data = {
                    'action':'wpvividstg_copy_site',
                    'id': id
                };
                wpvivid_lock_unlock_push_ui('lock');
                wpvivid_post_request(ajax_data, function(data)
                {
                    wpvivid_lock_unlock_push_ui('unlock');
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        push_staging_site_id=id;
                        wpvivid_staging_js_fix('wpvivid_custom_staging_site', false, jsonarray.uploads_path, jsonarray.content_path, jsonarray.home_path, id);
                        jQuery('#'+id).find('.wpvivid-push-content').after(jQuery('#wpvivid_custom_staging_site'));
                        jQuery('#wpvivid_custom_staging_site').show();
                    }
                    else if (jsonarray.result === 'failed')
                    {
                        alert(jsonarray.error);
                    }

                    jQuery('#wpvivid_staging_list').find('.wpvivid-copy-staging-to-live-block').each(function()
                    {
                        var tmp_id = jQuery(this).parents('tr').attr('id');
                        if(id !== tmp_id) {
                            if(jQuery(this).hasClass('staging-site')){
                                var class_btn = 'staging-site';
                                var copy_btn = 'Copy the Staging Site to Live';
                                var update_btn = 'Update the Staging Site';
                                var tip_text = 'Tips: Click the \'Copy the Staging Site to Live\' button above to migrate the staging site to your live site. Click the \'Update the Staging Site\' button to update the live site to the staging site.';
                            }
                            else{
                                var class_btn = 'fresh-install';
                                var copy_btn = 'Copy the Fresh Install to Live';
                                var update_btn = 'Update the Fresh Install';
                                var tip_text = 'Tips: Click the \'Copy the Fresh Install to Live\' button above to migrate the fresh install to your live site. Click the \'Update the Fresh Install\' button to update the live site to the fresh install.';
                            }

                            if(jQuery(this).hasClass('mu-single')){
                                var mu_single_class = 'mu-single';
                            }
                            else{
                                var mu_single_class = '';
                            }

                            var tmp_html = '<div>' +
                                '<input class="button-primary wpvivid-copy-staging-to-live '+class_btn+' '+mu_single_class+'" type="button" value="'+copy_btn+'" style="margin-right: 10px;" />' +
                                '<input class="button-primary wpvivid-update-live-to-staging '+class_btn+' '+mu_single_class+'" type="button" value="'+update_btn+'" /></div>' +
                                '<div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>'+tip_text+'</span></div>';
                            jQuery(this).html(tmp_html);
                        }
                        else{
                            if(jQuery(this).hasClass('mu-single')){
                                jQuery('#wpvivid_custom_staging_site').find('.core-desc').html('If the staging site and the live site have the same version of WordPress. Then it is not necessary to update the WordPress core files to the staging site.');
                                jQuery('#wpvivid_custom_staging_site').find('.database-desc').html('All the tables that belong to the subsite.');
                                jQuery('#wpvivid_custom_staging_site').find('.themes-plugins-desc').html('All the plugins and themes files used by the MU network. Plugins and themes activated on the subsite will be updated to the staging site by default.');
                                jQuery('#wpvivid_custom_staging_site').find('.uploads-desc').html('Files under the "uploads" folder that the staging site needs.');
                                jQuery('#wpvivid_custom_staging_site').find('.content-desc').html('<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to update to the staging site, except for the wp-content/uploads folder.');
                                jQuery('#wpvivid_custom_staging_site').find('.additional-file-desc').html('<strong style="text-decoration:underline;"><i>Include</i></strong> additional files or folders you want to update to the staging site.');
                            }
                        }
                    });
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    wpvivid_lock_unlock_push_ui('unlock');
                    var error_message = wpvivid_output_ajaxerror('export the previously-exported settings', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            jQuery('#wpvivid_switch_create_staging_page').click(function(){
                switch_staging_tab('create_staging');
            });

            jQuery('#wpvivid_staging_list').on("click", '.wpvivid-copy-staging-to-live', function()
            {
                if(jQuery(this).hasClass('staging-site'))
                {
                    var select_text = 'Choose what to copy from the staging site to the live site';
                    var select_tip = 'Tips: Click the \'Copy Now\' button to migrate the staging site to your live site.';
                }
                else
                {
                    var select_text = 'Choose what to copy from the fresh install to the live site';
                    var select_tip = 'Tips: Click the \'Copy Now\' button to migrate the fresh install to your live site.';
                }

                if(jQuery(this).hasClass('mu-single')){
                    var mu_single_style = 'display: none;';
                    var class_single = 'mu-single';
                }
                else{
                    var mu_single_style = '';
                    var class_single = '';
                }

                var id = jQuery(this).parents('tr').attr('id');
                jQuery('#'+id).after(jQuery('#wpvivid_custom_staging_site'));
                jQuery('#wpvivid_custom_staging_site').hide();

                <?php
                if(!is_multisite()){
                ?>
                jQuery('#wpvivid_staging_list').find('.wpvivid-copy-staging-to-live-block').each(function()
                {
                    var tmp_id = jQuery(this).parents('tr').attr('id');
                    if(id !== tmp_id) {
                        if(jQuery(this).hasClass('staging-site')){
                            var class_btn = 'staging-site';
                            var copy_btn = 'Copy the Staging Site to Live';
                            var update_btn = 'Update the Staging Site';
                            var tip_text = 'Tips: Click the \'Copy the Staging Site to Live\' button above to migrate the staging site to your live site. Click the \'Update the Staging Site\' button to update the live site to the staging site.';
                        }
                        else{
                            var class_btn = 'fresh-install';
                            var copy_btn = 'Copy the Fresh Install to Live';
                            var update_btn = 'Update the Fresh Install';
                            var tip_text = 'Tips: Click the \'Copy the Fresh Install to Live\' button above to migrate the fresh install to your live site. Click the \'Update the Fresh Install\' button to update the live site to the fresh install.';
                        }

                        var tmp_html = '<div>' +
                            '<input class="button-primary wpvivid-copy-staging-to-live '+class_btn+' " type="button" value="'+copy_btn+'" style="margin-right: 10px;" />' +
                            '<input class="button-primary wpvivid-update-live-to-staging '+class_btn+' " type="button" value="'+update_btn+'" /></div>' +
                            '<div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>'+tip_text+'</span></div>';
                        jQuery(this).html(tmp_html);
                    }
                });
                <?php
                }
                ?>

                if(jQuery(this).hasClass('mu-single'))
                {
                    var html = '<div style="height:20px;display:block;margin-bottom:10px;"><strong>'+select_text+'</strong></div>\n'+
                        '<div>'+
                        '<fieldset style="box-sizing: border-box;margin:10px 10px 0 10px;">'+
                        '<div style="margin:auto;">'+
                        '<div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left; '+mu_single_style+'">'+
                        '<label>'+
                        '<input type="radio" name="'+id+'" value="push_custom" checked>'+
                        '<span>Advanced</span>'+
                        '</label>'+
                        '</div>'+
                        '<div style="clear: both;"></div>'+
                        '</div>'+
                        '</fieldset>'+
                        '</div>'+
                        '<div class="wpvivid-push-content"></div>'+
                        '<div class="staging-list-push '+class_single+'" style="margin-top:10px; float:left; margin-right: 10px;"><input class="button-primary" type="button" value="Copy Now" /></div>'+
                        '<div class="staging-go-back" style="margin-top:10px; float:left;"><input class="button-primary" type="button" value="Go Back" /></div>'+
                        '<div style="clear:both"></div>'+
                        '<div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>'+select_tip+'</span></div>';
                }
                else
                {
                    var html = '<div style="height:20px;display:block;margin-bottom:10px;"><strong>'+select_text+'</strong></div>\n'+
                        '<div>'+
                        '<fieldset style="box-sizing: border-box;margin:10px 10px 0 10px;">'+
                        '<div style="margin:auto;">'+
                        <?php
                        if(is_multisite())
                        {
                        ?>
                        '<div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left; '+mu_single_style+'">'+
                        '<label>'+
                        '<input type="radio" name="'+id+'" value="push_mu_site" checked>'+
                        '<span>Easy Mode</span>'+
                        '</label>'+
                        '</div>'+
                        '<small>'+
                        '<div class="wpvivid_tooltip wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left; margin-top: 4px; line-height: 100%; white-space: normal;">?'+
                        '<div class="wpvivid_tooltiptext">Quickly get started by choosing the entire MU database and custom files and/or specific subsites and pushing to the live site.</div>'+
                        '</div>'+
                        '</small>'+
                        '<div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left; '+mu_single_style+'">'+
                        '<label>'+
                        '<input type="radio" name="'+id+'" value="push_custom">'+
                        '<span>Advanced Push</span>'+
                        '</label>'+
                        '</div>'+
                        '<small>'+
                        '<div class="wpvivid_tooltip wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left; margin-top: 4px; line-height: 100%; white-space: normal;">?'+
                        '<div class="wpvivid_tooltiptext">Give you the freedom to choose custom files and database tables of the entire MU network and push to the live site.</div>'+
                        '</div>'+
                        '</small>'+
                        '<div style="clear: both;"></div>'+
                        '</div>'+
                        '</fieldset>'+
                        '</div>'+
                        '<div class="wpvivid-push-content"></div>'+
                        '<div class="staging-list-push '+class_single+'" style="margin-top:10px; float:left; margin-right: 10px;"><input class="button-primary" type="button" value="Copy Now" /></div>'+
                        '<div class="staging-go-back" style="margin-top:10px; float:left;"><input class="button-primary" type="button" value="Go Back" /></div>'+
                        '<div style="clear:both"></div>'+
                        '<div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>'+select_tip+'</span></div>';
                    <?php
                    }
                    else{
                    ?>
                    '<div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left;">'+
                    '<label>'+
                    '<input type="radio" name="'+id+'" value="push_standard" checked>'+
                    '<span>Database + Uploads folder</span>'+
                    '</label>'+
                    '</div>'+
                    '<div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left;">'+
                    '<label>'+
                    '<input type="radio" name="'+id+'" value="push_custom">'+
                    '<span>Custom Content</span>'+
                    '</label>'+
                    '</div>'+
                    '<div style="clear: both;"></div>'+
                    '</div>'+
                    '</fieldset>'+
                    '</div>'+
                    '<div class="wpvivid-push-content"></div>'+
                    '<div class="staging-list-push" style="margin-top:10px; float:left; margin-right: 10px;"><input class="button-primary" type="button" value="Copy Now" /></div>'+
                    '<div class="staging-go-back" style="margin-top:10px; float:left;"><input class="button-primary" type="button" value="Go Back" /></div>'+
                    '<div style="clear:both"></div>'+
                    '<div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>'+select_tip+'</span></div>';
                    <?php
                    }
                    ?>
                }

                jQuery('#wpvivid_staging_list').find('tr#'+id).find('.wpvivid-copy-staging-to-live-block').html(html);
                <?php
                if(is_multisite()){
                ?>
                if(jQuery(this).hasClass('mu-single'))
                {
                    wpvivid_push_site(id);
                }
                else
                {
                    wpvivid_get_mu_site_info(id,'true');
                }
                <?php
                }
                ?>
            });

            jQuery('#wpvivid_staging_list').on("click", '.wpvivid-update-live-to-staging', function()
            {
                if (jQuery(this).hasClass('staging-site')) {
                    var select_text = 'Choose what to update from the live site to the staging site';
                    var select_tip = 'Tips: Click the \'Update Now\' button to update the live site to the staging site.';
                }
                else {
                    var select_text = 'Choose what to update from the live site to the fresh install';
                    var select_tip = 'Tips: Click the \'Update Now\' button to update the live site to the fresh install.';
                }

                if (jQuery(this).hasClass('mu-single')) {
                    var mu_single_style = 'display: none;';
                    var class_single = 'mu-single';
                }
                else {
                    var mu_single_style = '';
                    var class_single = '';
                }

                var id = jQuery(this).parents('tr').attr('id');
                jQuery('#' + id).after(jQuery('#wpvivid_custom_staging_site'));
                jQuery('#wpvivid_custom_staging_site').hide();
                <?php
                if(!is_multisite()){
                ?>
                jQuery('#wpvivid_staging_list').find('.wpvivid-copy-staging-to-live-block').each(function () {
                    var tmp_id = jQuery(this).parents('tr').attr('id');
                    if (id !== tmp_id) {
                        if (jQuery(this).hasClass('staging-site')) {
                            var class_btn = 'staging-site';
                            var copy_btn = 'Copy the Staging Site to Live';
                            var update_btn = 'Update the Staging Site';
                            var tip_text = 'Tips: Click the \'Copy the Staging Site to Live\' button above to migrate the staging site to your live site. Click the \'Update the Staging Site\' button to update the live site to the staging site.';
                        }
                        else {
                            var class_btn = 'fresh-install';
                            var copy_btn = 'Copy the Fresh Install to Live';
                            var update_btn = 'Update the Fresh Install';
                            var tip_text = 'Tips: Click the \'Copy the Fresh Install to Live\' button above to migrate the fresh install to your live site. Click the \'Update the Fresh Install\' button to update the live site to the fresh install.';
                        }

                        var tmp_html = '<div>' +
                            '<input class="button-primary wpvivid-copy-staging-to-live ' + class_btn + ' " type="button" value="' + copy_btn + '" style="margin-right: 10px;" />' +
                            '<input class="button-primary wpvivid-update-live-to-staging ' + class_btn + ' " type="button" value="' + update_btn + '" /></div>' +
                            '<div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>' + tip_text + '</span></div>';
                        jQuery(this).html(tmp_html);
                    }
                });
                <?php
                }
                ?>

                if (jQuery(this).hasClass('mu-single')) {
                    var html = '<div style="height:20px;display:block;margin-bottom:10px;"><strong>' + select_text + '</strong></div>\n' +
                        '<div>' +
                        '<fieldset style="box-sizing: border-box;margin:10px 10px 0 10px;">' +
                        '<div style="margin:auto;">' +
                        '<div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left; ' + mu_single_style + '">' +
                        '<label>' +
                        '<input type="radio" name="' + id + '" value="update_custom" checked>' +
                        '<span>Advanced</span>' +
                        '</label>' +
                        '</div>' +
                        '<div style="clear: both;"></div>' +
                        '</div>' +
                        '</fieldset>' +
                        '</div>' +
                        '<div class="wpvivid-push-content"></div>' +
                        '<div class="staging-list-push ' + class_single + '" style="margin-top:10px; float:left; margin-right: 10px;"><input class="button-primary" type="button" value="Update Now" /></div>' +
                        '<div class="staging-go-back" style="margin-top:10px; float:left;"><input class="button-primary" type="button" value="Go Back" /></div>' +
                        '<div style="clear:both"></div>' +
                        '<div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>' + select_tip + '</span></div>';
                }
                else {
                    var html = '<div style="height:20px;display:block;margin-bottom:10px;"><strong>' + select_text + '</strong></div>\n' +
                        '<div>' +
                        '<fieldset style="box-sizing: border-box;margin:10px 10px 0 10px;">' +
                        '<div style="margin:auto;">' +
                        <?php
                        if(is_multisite())
                        {
                        ?>
                        '<div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left; ' + mu_single_style + '">' +
                        '<label>' +
                        '<input type="radio" name="' + id + '" value="update_mu_site" checked>' +
                        '<span>Easy Mode</span>' +
                        '</label>' +
                        '</div>' +
                        '<small>' +
                        '<div class="wpvivid_tooltip wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left; margin-top: 4px; line-height: 100%; white-space: normal;">?' +
                        '<div class="wpvivid_tooltiptext">Quickly get started by choosing the entire MU database and custom files and/or specific subsites and updating them to the staging site.</div>' +
                        '</div>' +
                        '</small>' +
                        '<div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left; ' + mu_single_style + '">' +
                        '<label>' +
                        '<input type="radio" name="' + id + '" value="update_custom">' +
                        '<span>Advanced Update</span>' +
                        '</label>' +
                        '</div>' +
                        '<small>' +
                        '<div class="wpvivid_tooltip wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left; margin-top: 4px; line-height: 100%; white-space: normal;">?' +
                        '<div class="wpvivid_tooltiptext">Give you the freedom to choose custom files and database tables of the entire MU network and update them to the staging site.</div>' +
                        '</div>' +
                        '</small>' +
                        '<div style="clear: both;"></div>' +
                        '</div>' +
                        '</fieldset>' +
                        '</div>' +
                        '<div class="wpvivid-push-content"></div>' +
                        '<div class="staging-list-push ' + class_single + '" style="margin-top:10px; float:left; margin-right: 10px;"><input class="button-primary" type="button" value="Update Now" /></div>' +
                        '<div class="staging-go-back" style="margin-top:10px; float:left;"><input class="button-primary" type="button" value="Go Back" /></div>' +
                        '<div style="clear:both"></div>' +
                        '<div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>' + select_tip + '</span></div>';
                    <?php
                    }
                    else{
                    ?>
                    '<div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left;">' +
                    '<label>' +
                    '<input type="radio" name="' + id + '" value="update_standard" checked>' +
                    '<span>Database + Uploads folder</span>' +
                    '</label>' +
                    '</div>' +
                    '<div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left;">' +
                    '<label>' +
                    '<input type="radio" name="' + id + '" value="update_custom">' +
                    '<span>Custom Content</span>' +
                    '</label>' +
                    '</div>' +
                    '<div style="clear: both;"></div>' +
                    '</div>' +
                    '</fieldset>' +
                    '</div>' +
                    '<div class="wpvivid-push-content"></div>' +
                    '<div class="staging-list-push" style="margin-top:10px; float:left; margin-right: 10px;"><input class="button-primary" type="button" value="Update Now" /></div>' +
                    '<div class="staging-go-back" style="margin-top:10px; float:left;"><input class="button-primary" type="button" value="Go Back" /></div>' +
                    '<div style="clear:both"></div>' +
                    '<div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>' + select_tip + '</span></div>';
                    <?php
                    }
                    ?>
                }


                jQuery('#wpvivid_staging_list').find('tr#' + id).find('.wpvivid-copy-staging-to-live-block').html(html);
                <?php
                if(is_multisite())
                {
                ?>
                if (jQuery(this).hasClass('mu-single')) {
                    wpvivid_copy_site(id);
                }
                else {
                    wpvivid_get_mu_site_info(id, 'false');
                }

                <?php
                }
                ?>
            });

            jQuery('#wpvivid_staging_list').on("click", 'input:radio', function()
            {
                var id = jQuery(this).parents('tr').attr('id');
                jQuery('#wpvivid_staging_list').find('input:radio').each(function()
                {
                    var tmp_id = jQuery(this).parents('tr').attr('id');
                    if(id !== tmp_id){
                        jQuery('#wpvivid_staging_list').find('input:radio[name='+tmp_id+'][value=push_standard]').prop('checked', true);
                    }
                });
                var value = jQuery(this).attr('value');
                if(value === 'push_standard')
                {
                    jQuery('#wpvividstg_select_mu_staging_site').hide();
                    jQuery('#wpvivid_custom_staging_site').hide();
                }
                else if(value === 'push_custom')
                {
                    wpvivid_push_site(id);
                    jQuery('#wpvividstg_select_mu_staging_site').hide();
                    jQuery('#wpvivid_staging_list').find('.database-desc').html('It is recommended to copy all tables of the database to the live site.');
                    if(jQuery('#wpvivid_staging_list').find('tr#'+id).find('.wpvivid-copy-staging-to-live-block').hasClass('staging-site')){
                        var text = 'staging site';
                    }
                    else{
                        var text = 'fresh install';
                    }
                    <?php
                    if(is_multisite())
                    {
                    ?>
                    jQuery('#wpvivid_staging_list').find('.wpvivid-wordpress-core').html('WordPress MU Core');
                    jQuery('#wpvivid_staging_list').find('.core-desc').html('If the '+text+' and the live site have the same version of WordPress. Then it is not necessary to copy the WordPress MU core files to the live site.');
                    jQuery('#wpvivid_staging_list').find('.themes-plugins-desc').html('All the plugins and themes files used by the MU network. The activated plugins and themes will be copied to the live site by default. A child theme must be copied if it exists.');
                    jQuery('#wpvivid_staging_list').find('.uploads-desc').html('The folder where images and media files of the MU network are stored by default. All files will be copied to the live site by default. You can exclude folders you do not want to copy.');
                    jQuery('#wpvivid_staging_list').find('.content-desc').html('<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to copy to the live site, except for the wp-content/uploads folder.');
                    <?php
                    }
                    else{
                    ?>
                    jQuery('#wpvivid_staging_list').find('.core-desc').html('If the '+text+' and the live site have the same version of WordPress. Then it is not necessary to copy the WordPress core files to the live site. If they are not, it is recommended to copy the WordPress core files to the live site.');
                    jQuery('#wpvivid_staging_list').find('.themes-plugins-desc').html('The activated plugins and themes will be copied to the live site by default. The Child theme must be copied if it exists.');
                    jQuery('#wpvivid_staging_list').find('.uploads-desc').html('Images and media files are stored in the Uploads directory by default. All files are copied to the live site by default. You can exclude folders you do not want to copy.');
                    jQuery('#wpvivid_staging_list').find('.content-desc').html('<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to copy to the live site, except for the wp-content/uploads folder.');
                    <?php
                    }
                    ?>
                    jQuery('#wpvivid_staging_list').find('.additional-file-desc').html('<strong style="text-decoration:underline;"><i>Include</i></strong> additional files or folders you want to copy to the live site.');
                }
                else if(value === 'push_mu_site')
                {
                    wpvivid_get_mu_site_info(id,'true');
                    jQuery('#wpvivid_custom_staging_site').hide();
                }
                else if(value === 'update_standard')
                {
                    jQuery('#wpvividstg_select_mu_staging_site').hide();
                    jQuery('#wpvivid_custom_staging_site').hide();
                }
                else if(value === 'update_custom')
                {
                    //wpvivid_push_site(id);
                    wpvivid_copy_site(id);
                    jQuery('#wpvividstg_select_mu_staging_site').hide();
                    if(jQuery('#wpvivid_staging_list').find('tr#'+id).find('.wpvivid-copy-staging-to-live-block').hasClass('staging-site')){
                        var text = 'staging site';
                    }
                    else{
                        var text = 'fresh install';
                    }
                    <?php
                    if(is_multisite())
                    {
                    ?>
                    jQuery('#wpvivid_staging_list').find('.wpvivid-wordpress-core').html('WordPress MU Core');
                    jQuery('#wpvivid_staging_list').find('.core-desc').html('If the '+text+' and the live site have the same version of WordPress. Then it is not necessary to update the WordPress MU core files to the '+text+'.');
                    jQuery('#wpvivid_staging_list').find('.database-desc').html('All the tables in the WordPress MU database. It is recommended to update all the tables to the '+text+'.');
                    jQuery('#wpvivid_staging_list').find('.themes-plugins-desc').html('All the plugins and themes files used by the MU network. The activated plugins and themes will be updated to the '+text+' by default. A child theme must be updated if it exists.');
                    jQuery('#wpvivid_staging_list').find('.uploads-desc').html('The folder where images and media files of the MU network are stored by default. All files will be updated to the '+text+' by default. You can exclude folders you do not want to update.');
                    jQuery('#wpvivid_staging_list').find('.content-desc').html('<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to update to the '+text+', except for the wp-content/uploads folder');
                    jQuery('#wpvivid_staging_list').find('.additional-file-desc').html('<strong style="text-decoration:underline;"><i>Include</i></strong> additional files or folders you want to update to the '+text+'.');
                    <?php
                    }
                    else{
                    ?>
                    jQuery('#wpvivid_staging_list').find('.core-desc').html('If the '+text+' and the live site have the same version of WordPress. Then it is not necessary to update the WordPress core files to the '+text+'.');
                    jQuery('#wpvivid_staging_list').find('.database-desc').html('It is recommended to update all tables of the database to the '+text+'.');
                    jQuery('#wpvivid_staging_list').find('.themes-plugins-desc').html('The activated plugins and themes will be updated to the '+text+' by default. The Child theme must be copied if it exists.');
                    jQuery('#wpvivid_staging_list').find('.uploads-desc').html('Images and media files are stored in the Uploads directory by default. All files are copied to the '+text+' by default. You can exclude folders you do not want to copy.');
                    jQuery('#wpvivid_staging_list').find('.content-desc').html('<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to update to the '+text+', except for the wp-content/uploads folder.');
                    jQuery('#wpvivid_staging_list').find('.additional-file-desc').html('<strong style="text-decoration:underline;"><i>Include</i></strong> additional files or folders you want to update to the '+text+'.');
                    <?php
                    }
                    ?>
                }
                else if(value === 'update_mu_site')
                {
                    wpvivid_get_mu_site_info(id,'false');
                    jQuery('#wpvivid_custom_staging_site').hide();
                }
            });

            jQuery('#wpvivid_staging_list').on("click", '.staging-list-push input', function()
            {
                var btn_name = jQuery(this).val();
                if(btn_name === 'Copy Now'){
                    var descript = 'Click OK to start pushing the staging site to live.';
                }
                else{
                    var descript = 'Click OK to start updating the staging site.';
                }

                var ret = confirm(descript);
                if(ret === true) {
                    var id = jQuery(this).closest('tr').attr('id');
                    push_staging_site_id = id;
                    jQuery('#wpvivid_staging_notice').hide();
                    if (jQuery(this).closest('div').hasClass('mu-single')) {
                        var mu_single = true;
                    }
                    else {
                        var mu_single = false;
                    }
                    wpvivid_push_start_staging(mu_single);
                }
            });

            jQuery('#wpvivid_staging_list').on("click", '.staging-go-back', function(){
                location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=WPvivid_Staging'; ?>';
            });

            function wpvivid_delete_staging_site_lock_unlock(id, action){
                if(action === 'lock'){
                    jQuery('#wpvivid_staging_list').css({'pointer-events': 'none', 'opacity': '0.4'});
                }
                else{
                    jQuery('#wpvivid_staging_list').css({'pointer-events': 'auto', 'opacity': '1'});
                }
            }

            jQuery('#wpvivid_staging_list').on("click", '.wpvivid-delete-staging-site', function(){
                var descript = 'Are you sure to delete this staging site?';
                var ret = confirm(descript);
                if (ret === true) {
                    var id = jQuery(this).parent().attr('class');
                    var ajax_data = {
                        'action': 'wpvividstg_delete_site',
                        'id': id
                    };
                    wpvivid_delete_staging_site_lock_unlock(id, 'lock');
                    wpvivid_post_request(ajax_data, function (data) {
                        wpvivid_delete_staging_site_lock_unlock(id, 'unlock');
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success') {
                            //jQuery('#wpvivid_staging_list').html(jsonarray.html);
                            location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=WPvivid_Staging'; ?>';
                        }
                        else if (jsonarray.result === 'failed') {
                            alert(jsonarray.error);
                        }
                    }, function (XMLHttpRequest, textStatus, errorThrown) {
                        wpvivid_delete_staging_site_lock_unlock(id, 'unlock');
                        var error_message = wpvivid_output_ajaxerror('export the previously-exported settings', textStatus, errorThrown);
                        alert(error_message);
                    });
                }
            });
        </script>
        <?php
    }

    public function get_staging_site_data()
    {
        if(is_multisite())
        {
            switch_to_blog(get_main_network_id());
            $staging=get_option('wpvivid_staging_data',false);
            restore_current_blog();
        }
        else
        {
            $staging=get_option('wpvivid_staging_data',false);
        }

        return $staging;
    }

    public function get_custom_database_tables_info()
    {
        $this->ajax_check_security();
        try {
            global $wpdb;
            $db = array();
            $use_additional_db = false;
            $staging_site_id = $_POST['id'];
            if(empty($_POST['id']))
            {
                $get_site_mu_single=false;
            }
            else
            {
                $task = new WPvivid_Staging_Task_Ex($staging_site_id);
                $site_id=$task->get_site_mu_single_site_id();
                $get_site_mu_single=$task->get_site_mu_single();
            }


            if (isset($_POST['is_staging']) && !empty($_POST['is_staging']) && is_string($_POST['is_staging'])&&$_POST['is_staging'] == '1')
            {
                $base_prefix = $task->get_site_prefix();
            }
            else
            {
                $base_prefix=$wpdb->base_prefix;
            }

            if (isset($_POST['is_staging']) && !empty($_POST['is_staging']) && is_string($_POST['is_staging']))
            {
                if ($_POST['is_staging'] == '1')
                {
                    $is_staging_site = true;

                    $prefix = $task->get_site_prefix();

                    $db = $task->get_site_db_connect();
                    if ($db['use_additional_db'] !== false)
                    {
                        $use_additional_db = true;
                    } else {
                        $use_additional_db = false;
                    }
                } else {
                    $is_staging_site = false;
                    if (is_multisite())
                    {
                        if($get_site_mu_single)
                        {
                            $prefix = $wpdb->get_blog_prefix($site_id);
                        }
                        else
                        {
                            $prefix = $wpdb->base_prefix;
                        }
                    } else {
                        $prefix = $wpdb->get_blog_prefix(0);
                    }
                }
            } else {
                $is_staging_site = false;
                if (is_multisite())
                {
                    if($get_site_mu_single)
                    {
                        $site_id=$task->get_site_mu_single_site_id();
                        $prefix = $wpdb->get_blog_prefix($site_id);
                    }
                    else
                    {
                        $prefix = $wpdb->base_prefix;
                    }
                } else {
                    $prefix = $wpdb->get_blog_prefix(0);
                }
            }

            $ret['result'] = 'success';
            $ret['html'] = '';
            if (empty($prefix)) {
                echo json_encode($ret);
                die();
            }

            $base_table = '';
            $woo_table = '';
            $other_table = '';
            $default_table = array($prefix . 'commentmeta', $prefix . 'comments', $prefix . 'links', $prefix . 'options', $prefix . 'postmeta', $prefix . 'posts', $prefix . 'term_relationships',
                $prefix . 'term_taxonomy', $prefix . 'termmeta', $prefix . 'terms', $prefix . 'usermeta', $prefix . 'users');
            $woo_table_arr = array($prefix.'actionscheduler_actions', $prefix.'actionscheduler_claims', $prefix.'actionscheduler_groups', $prefix.'actionscheduler_logs', $prefix.'aelia_dismissed_messages',
                $prefix.'aelia_exchange_rates_history', $prefix.'automatewoo_abandoned_carts', $prefix.'automatewoo_customer_meta', $prefix.'automatewoo_customers', $prefix.'automatewoo_events',
                $prefix.'automatewoo_guest_meta', $prefix.'automatewoo_guests', $prefix.'automatewoo_log_meta', $prefix.'automatewoo_logs', $prefix.'automatewoo_queue', $prefix.'automatewoo_queue_meta',
                $prefix.'automatewoo_unsubscribes', $prefix.'wc_admin_note_actions', $prefix.'wc_admin_notes', $prefix.'wc_am_api_activation', $prefix.'wc_am_api_resource', $prefix.'wc_am_associated_api_key',
                $prefix.'wc_am_secure_hash', $prefix.'wc_category_lookup', $prefix.'wc_customer_lookup', $prefix.'wc_download_log', $prefix.'wc_order_coupon_lookup', $prefix.'wc_order_product_lookup',
                $prefix.'wc_order_stats', $prefix.'wc_order_tax_lookup', $prefix.'wc_product_meta_lookup', $prefix.'wc_reserved_stock', $prefix.'wc_tax_rate_classes', $prefix.'wc_webhooks',
                $prefix.'woocommerce_api_keys', $prefix.'woocommerce_attribute_taxonomies', $prefix.'woocommerce_downloadable_product_permissions', $prefix.'woocommerce_log', $prefix.'woocommerce_order_itemmeta',
                $prefix.'woocommerce_order_items', $prefix.'woocommerce_payment_tokenmeta', $prefix.'woocommerce_payment_tokens', $prefix.'woocommerce_sessions', $prefix.'woocommerce_shipping_zone_locations',
                $prefix.'woocommerce_shipping_zone_methods', $prefix.'woocommerce_shipping_zones', $prefix.'woocommerce_tax_rate_locations', $prefix.'woocommerce_tax_rates');

            if ($is_staging_site) {
                $staging_option = self::wpvivid_get_push_staging_history();
                if (empty($staging_option)) {
                    $staging_option = array();
                }
                if ($use_additional_db) {
                    $handle = new wpdb($db['dbuser'], $db['dbpassword'], $db['dbname'], $db['dbhost']);
                    $tables = $handle->get_results('SHOW TABLE STATUS', ARRAY_A);
                } else {
                    $tables = $wpdb->get_results('SHOW TABLE STATUS', ARRAY_A);
                }
            } else {
                $staging_option = self::wpvivid_get_staging_history();
                if (empty($staging_option)) {
                    $staging_option = array();
                }
                $tables = $wpdb->get_results('SHOW TABLE STATUS', ARRAY_A);
            }

            if (is_null($tables)) {
                $ret['result'] = 'failed';
                $ret['error'] = 'Failed to retrieve the table information for the database. Please try again.';
                echo json_encode($ret);
                die();
            }

            $tables_info = array();
            $has_base_table = false;
            $has_woo_table = false;
            $has_other_table = false;
            $base_count = 0;
            $woo_count = 0;
            $other_count = 0;
            $base_table_all_check = true;
            $woo_table_all_check = true;
            $other_table_all_check = true;
            foreach ($tables as $row)
            {
                if (preg_match('/^(?!' . $base_prefix . ')/', $row["Name"]) == 1)
                {
                    continue;
                }

                if($get_site_mu_single)
                {
                    $site_id=$task->get_site_mu_single_site_id();

                    if(!is_main_site($site_id))
                    {
                        if ( 1 == preg_match('/^' . $prefix . '/', $row["Name"]) )
                        {
                        }
                        else if ( 1 == preg_match('/^' . $base_prefix . '\d+_/', $row["Name"]) )
                        {
                            continue;
                        }
                        else
                        {
                            if($row["Name"]==$base_prefix.'users'||$row["Name"]==$base_prefix.'usermeta')
                            {

                            }
                            else
                            {
                                continue;
                            }
                        }
                    }
                    else
                    {
                        if ( 1 == preg_match('/^' . $base_prefix . '\d+_/', $row["Name"]) )
                        {
                            continue;
                        }
                        else
                        {
                            if($row["Name"]==$base_prefix.'blogs')
                                continue;
                            if($row["Name"]==$base_prefix.'blogmeta')
                                continue;
                            if($row["Name"]==$base_prefix.'sitemeta')
                                continue;
                            if($row["Name"]==$base_prefix.'site')
                                continue;
                        }
                    }
                }


                $tables_info[$row["Name"]]["Rows"] = $row["Rows"];
                $tables_info[$row["Name"]]["Data_length"] = size_format($row["Data_length"] + $row["Index_length"], 2);

                $checked = 'checked';
                if (!empty($staging_option['database_list'])) {
                    if ($is_staging_site) {
                        $tmp_row = $row["Name"];

                        $tmp_row = str_replace($base_prefix, $wpdb->base_prefix, $tmp_row);
                        if (in_array($tmp_row, $staging_option['database_list'])) {
                            $checked = '';
                        }
                    }
                    else if (in_array($row["Name"], $staging_option['database_list'])) {
                        $checked = '';
                    }
                }

                if (in_array($row["Name"], $default_table)) {
                    if ($checked == '') {
                        $base_table_all_check = false;
                    }
                    $has_base_table = true;

                    $base_table .= '<div class="wpvivid-custom-database-table-column">
                                        <label style="width:100%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap; padding-top: 3px;" 
                                        title="' . esc_html($row["Name"]) . '|Rows:' . $row["Rows"] . '|Size:' . $tables_info[$row["Name"]]["Data_length"] . '">
                                        <input type="checkbox" option="base_db" name="Database" value="' . esc_html($row["Name"]) . '" ' . esc_html($checked) . ' />
                                        ' . esc_html($row["Name"]) . '|Rows:' . $row["Rows"] . '|Size:' . $tables_info[$row["Name"]]["Data_length"] . '
                                        </label>
                                    </div>';
                    $base_count++;
                } else if(in_array($row['Name'], $woo_table_arr)){
                    if ($checked == '') {
                        $woo_table_all_check = false;
                    }
                    $has_woo_table = true;
                    $woo_table .= '<div class="wpvivid-custom-database-table-column">
                                        <label style="width:100%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap; padding-top: 3px;"
                                        title="' . esc_html($row["Name"]) . '|Rows:' . $row["Rows"] . '|Size:' . $tables_info[$row["Name"]]["Data_length"] . '">
                                        <input type="checkbox" option="woo_db" name="Database" value="' . esc_html($row["Name"]) . '" ' . esc_html($checked) . ' />
                                        ' . esc_html($row["Name"]) . '|Rows:' . $row["Rows"] . '|Size:' . $tables_info[$row["Name"]]["Data_length"] . '
                                        </label>
                                    </div>';
                    $woo_count++;
                }
                else {
                    if ($checked == '') {
                        $other_table_all_check = false;
                    }
                    $has_other_table = true;
                    $other_table .= '<div class="wpvivid-custom-database-table-column">
                                        <label style="width:100%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap; padding-top: 3px;"
                                        title="' . esc_html($row["Name"]) . '|Rows:' . $row["Rows"] . '|Size:' . $tables_info[$row["Name"]]["Data_length"] . '">
                                        <input type="checkbox" option="other_db" name="Database" value="' . esc_html($row["Name"]) . '" ' . esc_html($checked) . ' />
                                        ' . esc_html($row["Name"]) . '|Rows:' . $row["Rows"] . '|Size:' . $tables_info[$row["Name"]]["Data_length"] . '
                                        </label>
                                    </div>';
                    $other_count++;
                }
            }

            $base_table .= '<div style="clear:both;"></div>';
            $woo_table .= '<div style="clear:both;"></div>';
            $other_table .= '<div style="clear:both;"></div>';

            $base_table_html = '';
            $woo_table_html = '';
            $other_table_html = '';
            if ($has_base_table) {
                $base_all_check = '';
                if ($base_table_all_check) {
                    $base_all_check = 'checked';
                }
                $base_table_html .= '<div class="wpvivid-custom-database-wp-table-header" style="border:1px solid #e5e5e5;">
                                        <div>
                                            <div style="float: left; margin-right: 10px;">
                                                <label><input type="checkbox" class="wpvivid-database-table-check wpvivid-database-base-table-check" ' . esc_attr($base_all_check) . ' />WordPress Tables</label>
                                            </div>
                                            <small>
                                                <div class="wpvivid_tooltip" style="float: left; margin-top: 6px; line-height: 100%; white-space: normal;">?
                                                    <div class="wpvivid_tooltiptext">The tables are created by WordPress. Select all unless you are a WordPress specialist.</div>
                                                </div>
                                            </small>
                                            <div style="clear: both;"></div>
                                        </div>
                                     </div>
                                     <div style="clear: both;"></div>
                                     <div class="wpvivid-database-table-addon" style="border:1px solid #e5e5e5; border-top: none; padding: 0 4px 4px 4px; max-height: 300px; overflow-y: auto; overflow-x: hidden;">
                                        ' . $base_table . '
                                     </div>';
            }

            if($has_woo_table) {
                $woo_all_check = '';
                if ($woo_table_all_check) {
                    $woo_all_check = 'checked';
                }
                $woo_table_html .= '<div class="wpvivid-custom-database-wp-table-header" style="border:1px solid #e5e5e5;">
                                        <div>
                                            <div style="float: left; margin-right: 10px;">
                                                <label><input type="checkbox" class="wpvivid-database-table-check wpvivid-database-woo-table-check" ' . esc_attr($woo_all_check) . ' />WooCommerce Tables</label>
                                            </div>
                                            <small>
                                                <div class="wpvivid_tooltip" style="float: left; margin-top: 6px; line-height: 100%; white-space: normal;">?
                                                    <div class="wpvivid_tooltiptext">WooCommerce tables are created by WooCommerce, please select with caution.</div>
                                                </div>
                                            </small>
                                            <div style="clear: both;"></div>
                                        </div>
                                     </div>
                                     <div style="clear: both;"></div>
                                     <div class="wpvivid-database-table-addon" style="border:1px solid #e5e5e5; border-top: none; padding: 0 4px 4px 4px; max-height: 300px; overflow-y: auto; overflow-x: hidden;">
                                        ' . $woo_table . '
                                     </div>';
            }

            if ($has_other_table) {
                $other_all_check = '';
                if ($other_table_all_check) {
                    $other_all_check = 'checked';
                }
                $other_table_html .= '<div class="wpvivid-custom-database-other-table-header" style="border:1px solid #e5e5e5;">
                                        <div>
                                            <div style="float: left; margin-right: 10px;">
                                                <label><input type="checkbox" class="wpvivid-database-table-check wpvivid-database-other-table-check" ' . esc_attr($other_all_check) . ' />Other Tables</label>
                                            </div>
                                            <small>
                                                <div class="wpvivid_tooltip" style="float: left; margin-top: 6px; line-height: 100%; white-space: normal;">?
                                                    <div class="wpvivid_tooltiptext">Other tables are created by your plugins or themes, please select with caution.</div>
                                                </div>
                                            </small>
                                            <div style="clear: both;"></div>
                                        </div>
                                     </div>
                                     <div style="clear: both;"></div>
                                     <div class="wpvivid-database-table-addon" style="border:1px solid #e5e5e5; border-top: none; padding: 0 4px 4px 4px; max-height: 300px; overflow-y: auto; overflow-x: hidden;">
                                        ' . $other_table . '
                                     </div>';
            }
            $div = '<div style="clear:both;"></div>';
            $div .= '<div style="margin-bottom: 10px;"></div>';
            $ret['html'] = $base_table_html . $div . $woo_table_html . $div . $other_table_html;
            $ret['tables_info'] = $tables_info;
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function wpvivid_replace_directory( $path ) {
        return preg_replace( '/[\\\\]+/', '/', $path );
    }

    public function getPath( $path, $wpcontentDir, $directory ) {
        $realPath = $this->wpvivid_replace_directory($directory->getRealPath());
        if( false === strpos( $realPath, $path ) ) {
            return false;
        }

        $path = str_replace( $wpcontentDir . '/', null, $this->wpvivid_replace_directory($directory->getRealPath()) );
        // Using strpos() for symbolic links as they could create nasty stuff in nix stuff for directory structures
        if( !$directory->isDir() ||
            strlen( $path ) < 1 ||
            (strpos( $this->wpvivid_replace_directory($directory->getRealPath()), $wpcontentDir . '/' . 'plugins' ) !== 0 &&
                strpos( $this->wpvivid_replace_directory($directory->getRealPath()), $wpcontentDir . '/' . 'themes' ) !== 0 &&
                strpos( $this->wpvivid_replace_directory($directory->getRealPath()), $wpcontentDir . '/' . 'uploads' ) !== 0 )
        ) {
            return false;
        }

        return $path;
    }

    public function wpvivid_search_staging_theme_directories($wpvivid_staging_themes_dir){
        if ( empty( $wpvivid_staging_themes_dir ) ) {
            return false;
        }
        $found_themes = array();
        $wpvivid_staging_themes_dir = (array) $wpvivid_staging_themes_dir;
        foreach ( $wpvivid_staging_themes_dir as $theme_root ) {
            $dirs = @ scandir( $theme_root );
            if ( ! $dirs ) {
                continue;
            }
            foreach ( $dirs as $dir ) {
                if ( ! is_dir( $theme_root . '/' . $dir ) || $dir[0] == '.' || $dir == 'CVS' ) {
                    continue;
                }
                if ( file_exists( $theme_root . '/' . $dir . '/style.css' ) ) {
                    $found_themes[ $dir ] = array(
                        'theme_file' => $dir . '/style.css',
                        'theme_root' => $theme_root,
                    );
                }
                else {
                    $found_theme = false;
                    $sub_dirs = @ scandir( $theme_root . '/' . $dir );
                    if ( ! $sub_dirs ) {
                        continue;
                    }
                    foreach ( $sub_dirs as $sub_dir ) {
                        if ( ! is_dir( $theme_root . '/' . $dir . '/' . $sub_dir ) || $dir[0] == '.' || $dir == 'CVS' ) {
                            continue;
                        }
                        if ( ! file_exists( $theme_root . '/' . $dir . '/' . $sub_dir . '/style.css' ) ) {
                            continue;
                        }
                        $found_themes[ $dir . '/' . $sub_dir ] = array(
                            'theme_file' => $dir . '/' . $sub_dir . '/style.css',
                            'theme_root' => $theme_root,
                        );
                        $found_theme = true;
                    }
                    if ( ! $found_theme ) {
                        $found_themes[ $dir ] = array(
                            'theme_file' => $dir . '/style.css',
                            'theme_root' => $theme_root,
                        );
                    }
                }
            }
        }
        asort( $found_themes );
        return $found_themes;
    }

    public function get_staging_themes_info($wpvivid_staging_themes_dir){
        $themes = array();
        $theme_directories = $this->wpvivid_search_staging_theme_directories($wpvivid_staging_themes_dir);
        if ( !empty( $theme_directories ) ) {
            foreach ( $theme_directories as $theme => $theme_root ) {
                $themes[ $theme ] = $theme_root['theme_root'] . '/' . $theme;
                $themes[ $theme ] = new WP_Theme( $theme, $theme_root['theme_root'] );
            }
        }
        return $themes;
    }

    public function get_staging_plugins_info($wpvivid_stating_plugins_dir){
        $wp_plugins  = array();
        $plugin_root = $wpvivid_stating_plugins_dir;
        $plugins_dir  = @ opendir( $plugin_root );
        $plugin_files = array();
        if ( $plugins_dir ) {
            while ( ( $file = readdir( $plugins_dir ) ) !== false ) {
                if ( substr( $file, 0, 1 ) == '.' ) {
                    continue;
                }
                if ( is_dir( $plugin_root . '/' . $file ) ) {
                    $plugins_subdir = @ opendir( $plugin_root . '/' . $file );
                    if ( $plugins_subdir ) {
                        while ( ( $subfile = readdir( $plugins_subdir ) ) !== false ) {
                            if ( substr( $subfile, 0, 1 ) == '.' ) {
                                continue;
                            }
                            if ( substr( $subfile, -4 ) == '.php' ) {
                                $plugin_files[] = "$file/$subfile";
                            }
                        }
                        closedir( $plugins_subdir );
                    }
                } else {
                    if ( substr( $file, -4 ) == '.php' ) {
                        $plugin_files[] = $file;
                    }
                }
            }
            closedir( $plugins_dir );
        }
        if ( !empty( $plugin_files ) ) {
            foreach ( $plugin_files as $plugin_file ) {
                if ( ! is_readable( "$plugin_root/$plugin_file" ) ) {
                    continue;
                }

                $plugin_data = get_plugin_data( "$plugin_root/$plugin_file", false, false );

                if ( empty( $plugin_data['Name'] ) ) {
                    continue;
                }

                $wp_plugins[ plugin_basename( $plugin_file ) ] = $plugin_data;
            }
        }
        return $wp_plugins;
    }

    public function get_staging_directory_info($path){
        $wpcontentDir = $path.DIRECTORY_SEPARATOR.'wp-content';
        $wpcontentDir = str_replace('\\', '/', $wpcontentDir);
        $tmp_path = str_replace('\\', '/', $path);
        if(!file_exists($wpcontentDir)){
            //return error
        }
        else {
            $directories = new \DirectoryIterator($wpcontentDir);
        }
        $wpvivid_staging_themes_dir  = '';
        $wpvivid_stating_plugins_dir = '';
        foreach ( $directories as $directory ) {
            if( false === ($path = $this->getPath( $tmp_path, $wpcontentDir, $directory )) ) {
                continue;
            }
            if($directory == 'themes'){
                $wpvivid_staging_themes_dir  = $wpcontentDir . '/' . 'themes';
            }
            if($directory == 'plugins'){
                $wpvivid_stating_plugins_dir = $wpcontentDir . '/' . 'plugins';
            }
        }
        $ret['themes_list']  = $this->get_staging_themes_info($wpvivid_staging_themes_dir);
        $ret['plugins_list'] = $this->get_staging_plugins_info($wpvivid_stating_plugins_dir);
        return $ret;
    }

    public function get_custom_themes_plugins_info()
    {
       $this->ajax_check_security();
        try
        {
            if (isset($_POST['is_staging']) && !empty($_POST['is_staging']))
            {
                if ($_POST['is_staging'] == '1')
                {
                    $is_staging_site = true;
                    $staging_site_id = $_POST['id'];

                    $task = new WPvivid_Staging_Task_Ex($staging_site_id);
                    $ret = $this->get_staging_directory_info($task->get_site_path());
                } else {
                    $is_staging_site = false;
                }
            } else {
                $is_staging_site = false;
            }

            if ($is_staging_site)
            {
                $staging_option = array();
            } else {
                $staging_option = self::wpvivid_get_staging_history();
                if (empty($staging_option))
                {
                    $staging_option = array();
                }
            }

            $checkbox_disable = $is_staging_site == false ? ' disabled' : '';
            $themes_path = $is_staging_site == false ? get_theme_root() : $_POST['staging_path'] . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'themes';
            $has_themes = false;
            $themes_table = '';
            $themes_table_html = '';
            $themes_count = 0;
            $themes_all_check = 'checked';
            $themes_info = array();

            $themes = $is_staging_site == false ? wp_get_themes() : $ret['themes_list'];

            if (!empty($themes))
            {
                $has_themes = true;
            }

            foreach ($themes as $theme)
            {
                $file = $theme->get_stylesheet();
                $themes_info[$file] = $this->get_theme_plugin_info($themes_path . DIRECTORY_SEPARATOR . $file);
                $parent=$theme->parent();
                $themes_info[$file]['parent']=$parent;
                $themes_info[$file]['parent_file']=$theme->get_template();
                $themes_info[$file]['child']=array();

                if(isset($_POST['subsite']))
                {
                    switch_to_blog($_POST['subsite']);
                    $ct = wp_get_theme();
                    if( $ct->get_stylesheet()==$file)
                    {
                        $themes_info[$file]['active'] = 1;
                    }
                    else
                    {
                        $themes_info[$file]['active'] = 0;
                    }
                    restore_current_blog();
                }
                else
                {
                    $themes_info[$file]['active'] = 1;
                }
            }

            foreach ($themes_info as $file => $info)
            {
                if($info['active']&&$info['parent']!=false)
                {
                    $themes_info[$info['parent_file']]['active']=1;
                    $themes_info[$info['parent_file']]['child'][]=$file;
                }
            }

            foreach ($themes_info as $file => $info) {
                $checked = '';

                if ($info['active'] == 1) {
                    $checked = 'checked';
                }

                if (!empty($staging_option['themes_list'])) {
                    if (in_array($file, $staging_option['themes_list'])) {
                        $checked = '';
                    }
                }

                if (empty($checked)) {
                    $themes_all_check = '';
                }
                $themes_table .= '<div class="wpvivid-custom-database-table-column">
                                        <label style="width:100%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap; padding-top: 3px;"
                                        title="' . esc_html($file) . '|Size:' . size_format($info["size"], 2) . '">
                                        <input type="checkbox" option="themes" name="Themes" value="' . esc_attr($file) . '" ' . esc_html($checked) . ' />
                                        ' . esc_html($file) . '|Size:' . size_format($info["size"], 2) . '</label></div>';
                $themes_count++;
            }
            $themes_table .= '<div style="clear:both;"></div>';
            $ret['result'] = 'success';
            $ret['themes_info'] = $themes_info;
            if ($has_themes) {
                $themes_table_html .= '<div class="wpvivid-custom-database-wp-table-header" style="border:1px solid #e5e5e5;">
                                        <label><input type="checkbox" class="wpvivid-themes-plugins-table-check wpvivid-themes-table-check" ' . esc_attr($themes_all_check . $checkbox_disable) . ' />Themes</label>
                                     </div>
                                     <div class="wpvivid-database-table-addon" style="border:1px solid #e5e5e5; border-top: none; padding: 0 4px 4px 4px; max-height: 300px; overflow-y: auto; overflow-x: hidden;">
                                        ' . $themes_table . '
                                     </div>';
            }
            $ret['html'] = $themes_table_html;

            $ret['html'] .= '<div style="clear:both;"></div>';
            $ret['html'] .= '<div style="margin-bottom: 10px;"></div>';

            $has_plugins = false;
            $plugins_table = '';
            $plugins_table_html = '';
            $path = $is_staging_site == false ? WP_PLUGIN_DIR : $_POST['staging_path'] . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins';
            $plugin_count = 0;
            $plugins_all_check = 'checked';
            $plugin_info = array();

            if (!function_exists('get_plugins'))
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            $plugins = $is_staging_site == false ? get_plugins() : $ret['plugins_list'];

            if (!empty($plugins)) {
                $has_plugins = true;
            }

            if(isset($_POST['subsite']))
            {
                switch_to_blog($_POST['subsite']);
                $current   = get_option( 'active_plugins', array() );
                restore_current_blog();
            }
            else
            {
                $current   = get_option( 'active_plugins', array() );
            }


            foreach ($plugins as $key => $plugin)
            {
                $slug = dirname($key);
                if ($slug == '.')
                    continue;
                $plugin_info[$slug] = $this->get_theme_plugin_info($path . DIRECTORY_SEPARATOR . $slug);
                $plugin_info[$slug]['Name'] = $plugin['Name'];
                $plugin_info[$slug]['slug'] = $slug;

                if(isset($_POST['subsite']))
                {
                    if(in_array($key,$current))
                    {
                        $plugin_info[$slug]['active'] = 1;
                    }
                    else
                    {
                        $plugin_info[$slug]['active'] = 0;
                    }
                }
                else
                {
                    $plugin_info[$slug]['active'] = 1;
                }
            }

            foreach ($plugin_info as $slug => $info) {
                $checked = '';

                if ($info['active'] == 1) {
                    $checked = 'checked';
                }

                if (!empty($staging_option['plugins_list'])) {
                    if (in_array($slug, $staging_option['plugins_list'])) {
                        $checked = '';
                    }
                }

                if (empty($checked)) {
                    $plugins_all_check = '';
                }

                $disable_check = '';
                if ($info['slug'] == 'wpvivid-backuprestore' || $info['slug'] == 'wpvivid-backup-pro' || $info['slug'] == 'wpvivid-staging') {
                    $disable_check = 'disabled';
                    $checked = 'checked';
                }
                $plugins_table .= '<div class="wpvivid-custom-database-table-column">
                                        <label style="width:100%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap; padding-top: 3px;" 
                                        title="' . esc_html($info['Name']) . '|Size:' . size_format($info["size"], 2) . '">
                                        <input type="checkbox" option="plugins" name="Plugins" value="' . esc_attr($info['slug']) . '" ' . esc_html($checked) . ' ' . $disable_check . ' />
                                        ' . esc_html($info['Name']) . '|Size:' . size_format($info["size"], 2) . '</label>
                                    </div>';
                $plugin_count++;
            }

            $plugins_table .= '<div style="clear:both;"></div>';
            $ret['plugin_info'] = $plugin_info;
            if ($has_plugins) {
                $plugins_table_html .= '<div class="wpvivid-custom-database-other-table-header" style="border:1px solid #e5e5e5;">
                                        <label><input type="checkbox" class="wpvivid-themes-plugins-table-check wpvivid-plugins-table-check" ' . esc_attr($plugins_all_check . $checkbox_disable) . ' />Plugins</label>
                                     </div>
                                     <div class="wpvivid-database-table-addon" style="border:1px solid #e5e5e5; border-top: none; padding: 0 4px 4px 4px; max-height: 300px; overflow-y: auto; overflow-x: hidden;">
                                        ' . $plugins_table . '
                                     </div>';
            }
            $ret['html'] .= $plugins_table_html;
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function get_theme_plugin_info($root)
    {
        $theme_info['size']=$this->get_folder_size($root,0);
        return $theme_info;
    }

    public function get_folder_size($root,$size)
    {
        $count = 0;
        if(is_dir($root))
        {
            $handler = opendir($root);
            if($handler!==false)
            {
                while (($filename = readdir($handler)) !== false)
                {
                    if ($filename != "." && $filename != "..") {
                        $count++;

                        if (is_dir($root . DIRECTORY_SEPARATOR . $filename))
                        {
                            $size=$this->get_folder_size($root . DIRECTORY_SEPARATOR . $filename,$size);
                        } else {
                            $size+=filesize($root . DIRECTORY_SEPARATOR . $filename);
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }

        }
        return $size;
    }

    public function get_custom_dir_uploads_info(){
        $this->ajax_check_security();
        try {
            if (isset($_POST['is_staging'])) {
                $is_staging = $_POST['is_staging'];
                $node_array = array();

                if ($_POST['tree_node']['node']['id'] == '#') {
                    $path = ABSPATH;

                    if (!empty($_POST['tree_node']['path'])) {
                        $path = $_POST['tree_node']['path'];
                    }

                    $node_array[] = array(
                        'text' => basename($path),
                        'children' => true,
                        'id' => $path,
                        'icon' => 'jstree-folder',
                        'state' => array(
                            'opened' => true
                        )
                    );
                } else {
                    $path = $_POST['tree_node']['node']['id'];
                }

                if (file_exists($path)) {
                    $path = trailingslashit(str_replace('\\', '/', realpath($path)));
                    if ($dh = opendir($path)) {
                        while (substr($path, -1) == '/') {
                            $path = rtrim($path, '/');
                        }
                        $skip_paths = array(".", "..");

                        while (($value = readdir($dh)) !== false) {
                            trailingslashit(str_replace('\\', '/', $value));
                            if (!in_array($value, $skip_paths)) {
                                $custom_dir = $is_staging == false ? WP_CONTENT_DIR . '/' . WPVIVID_STAGING_DIR : $path . '/' . WPVIVID_STAGING_DIR;
                                $custom_dir = str_replace('\\', '/', $custom_dir);

                                $themes_dir = $is_staging == false ? get_theme_root() : $path . '/themes';
                                $themes_dir = trailingslashit(str_replace('\\', '/', $themes_dir));
                                $themes_dir = rtrim($themes_dir, '/');

                                $plugin_dir = $is_staging == false ? WP_PLUGIN_DIR : $path . '/plugins';
                                $plugin_dir = trailingslashit(str_replace('\\', '/', $plugin_dir));
                                $plugin_dir = rtrim($plugin_dir, '/');

                                if ($is_staging == false) {
                                    $upload_path = wp_upload_dir();
                                    $upload_path['basedir'] = trailingslashit(str_replace('\\', '/', $upload_path['basedir']));
                                    $upload_dir = rtrim($upload_path['basedir'], '/');
                                    $subsite_dir = rtrim($upload_path['basedir'], '/') . '/' . 'sites';
                                } else {
                                    $upload_dir = $path . '/uploads';
                                    $subsite_dir = $path . '/sites';
                                }
                                $exclude_dir = array($themes_dir, $plugin_dir, $upload_dir, $custom_dir, $subsite_dir);
                                if (is_dir($path . '/' . $value)) {
                                    if (!in_array($path . '/' . $value, $exclude_dir)) {
                                        $node['text'] = $value;
                                        $node['children'] = true;
                                        $node['id'] = $path . '/' . $value;
                                        $node['icon'] = 'jstree-folder';
                                        $node_array[] = $node;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $node_array = array();
                }

                $ret['nodes'] = $node_array;
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function get_custom_dir_additional_info(){
        $this->ajax_check_security();
        try {
            if (isset($_POST['is_staging'])) {
                $is_staging = $_POST['is_staging'];
                $node_array = array();

                if ($_POST['tree_node']['node']['id'] == '#') {
                    $path = ABSPATH;

                    if (!empty($_POST['tree_node']['path'])) {
                        $path = $_POST['tree_node']['path'];
                    }

                    $node_array[] = array(
                        'text' => basename($path),
                        'children' => true,
                        'id' => $path,
                        'icon' => 'jstree-folder',
                        'state' => array(
                            'opened' => true
                        )
                    );
                } else {
                    $path = $_POST['tree_node']['node']['id'];
                }

                if (file_exists($path)) {
                    $path = trailingslashit(str_replace('\\', '/', realpath($path)));

                    if ($dh = opendir($path)) {
                        while (substr($path, -1) == '/') {
                            $path = rtrim($path, '/');
                        }

                        $skip_paths = array(".", "..");

                        $file_array = array();

                        while (($value = readdir($dh)) !== false) {
                            trailingslashit(str_replace('\\', '/', $value));

                            if (!in_array($value, $skip_paths)) {
                                if (is_dir($path . '/' . $value)) {
                                    $wp_admin_path = $is_staging == false ? ABSPATH . 'wp-admin' : $path . '/wp-admin';
                                    $wp_admin_path = str_replace('\\', '/', $wp_admin_path);

                                    $wp_include_path = $is_staging == false ? ABSPATH . 'wp-includes' : $path . '/wp-includes';
                                    $wp_include_path = str_replace('\\', '/', $wp_include_path);

                                    $content_dir = $is_staging == false ? WP_CONTENT_DIR : $path . '/wp-content';
                                    $content_dir = str_replace('\\', '/', $content_dir);
                                    $content_dir = rtrim($content_dir, '/');

                                    $exclude_dir = array($wp_admin_path, $wp_include_path, $content_dir);
                                    if (!in_array($path . '/' . $value, $exclude_dir)) {
                                        $node_array[] = array(
                                            'text' => $value,
                                            'children' => true,
                                            'id' => $path . '/' . $value,
                                            'icon' => 'jstree-folder'
                                        );
                                    }

                                } else {
                                    $wp_admin_path = $is_staging == false ? ABSPATH : $path;
                                    $wp_admin_path = str_replace('\\', '/', $wp_admin_path);
                                    $wp_admin_path = rtrim($wp_admin_path, '/');
                                    $skip_path = rtrim($path, '/');

                                    if ($wp_admin_path == $skip_path) {
                                        continue;
                                    }
                                    $file_array[] = array(
                                        'text' => $value,
                                        'children' => false,
                                        'id' => $path . '/' . $value,
                                        'type' => 'file',
                                        'icon' => 'jstree-file'
                                    );
                                }
                            }
                        }
                        $node_array = array_merge($node_array, $file_array);
                    }
                } else {
                    $node_array = array();
                }

                $ret['nodes'] = $node_array;
                echo json_encode($ret);
                die();
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function staging_site()
    {
        $redirect=false;
        if(is_multisite())
        {
            switch_to_blog(get_main_network_id());
            $staging_init=get_option('wpvivid_staging_init', false);
            $staging_finish=get_option('wpvivid_staging_finish', false);
            restore_current_blog();
        }
        else
        {
            $staging_init=get_option('wpvivid_staging_init', false);
            $staging_finish=get_option('wpvivid_staging_finish', false);
        }

        if($staging_finish)
        {
            if ( function_exists( 'save_mod_rewrite_rules' ) ) {
                save_mod_rewrite_rules();
            }
            else{
                if(file_exists(ABSPATH . 'wp-admin/includes/misc.php')) {
                    require_once ABSPATH . 'wp-admin/includes/misc.php';
                }
                if ( function_exists( 'save_mod_rewrite_rules' ) ) {
                    save_mod_rewrite_rules();
                }
            }
            flush_rewrite_rules(true);
            delete_option('wpvivid_staging_finish');
            if(!$this->check_theme_exist())
            {
                $redirect=true;
            }
        }

        if($staging_init)
        {
            global $wp_rewrite;

            if($staging_init == 1){
                //create staging site
                $wp_rewrite->set_permalink_structure( null );
            }
            else{
                //push to live site
                $wp_rewrite->set_permalink_structure( $staging_init );
            }

            delete_option('wpvivid_staging_init');
        }

        $data=$this->get_staging_site_data();

        if($data!==false)
        {
            wp_enqueue_style( "wpvivid-admin-bar", WPVIVID_STAGING_PLUGIN_URL . "includes/css/wpvivid-admin-bar.css", array(), $this->version );
            if(!$this->is_login_page())
            {
                if(is_multisite())
                {
                    switch_to_blog(get_main_network_id());
                    $options=get_option('wpvivid_staging_options', false);
                    restore_current_blog();
                }
                else
                {
                    $options=get_option('wpvivid_staging_options',array());
                }

                if(isset($options['not_need_login'])&&!$options['not_need_login'])
                {
                    if( !current_user_can( 'manage_options' ) )
                    {
                        $this->output_login_page();
                    }
                }
                else
                {

                }
            }
        }

        if($redirect)
        {
            ?>
            <script>
                location.reload();
            </script>
            <?php
        }
    }

    public function check_theme_exist()
    {
        global $wp_theme_directories;
        $stylesheet = get_stylesheet();
        $theme_root = get_raw_theme_root( $stylesheet );
        if ( false === $theme_root ) {
            $theme_root = WP_CONTENT_DIR . '/themes';
        }
        elseif ( ! in_array( $theme_root, (array) $wp_theme_directories ) )
        {
            $theme_root = WP_CONTENT_DIR . $theme_root;
        }

        $theme_dir = $stylesheet;

        // Correct a situation where the theme is 'some-directory/some-theme' but 'some-directory' was passed in as part of the theme root instead.
        if ( ! in_array( $theme_root, (array) $wp_theme_directories ) && in_array( dirname( $theme_root ), (array) $wp_theme_directories ) ) {
            $stylesheet = basename( $theme_root ) . '/' .$theme_dir;
            $theme_root = dirname( $theme_root );
        }

        $theme_file       = $stylesheet . '/style.css';

        if( ! file_exists( $theme_root . '/' . $theme_file ) )
        {
            $themes=wp_get_themes();
            foreach ($themes as $theme)
            {
                switch_theme($theme->get_stylesheet());
                return false;
            }
        }
        return true;
    }

    public function is_login_page()
    {
        return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
    }

    public function wpvivid_logout_redirect()
    {
        $redirectTo = get_site_url();
        wp_logout();
        ?>
        <script>
            location.href='<?php echo $redirectTo; ?>';
        </script>
        <?php
    }

    public function output_login_page()
    {
        if(is_user_logged_in())
        {
            if(current_user_can( 'manage_options' ))
            {
                return false;
            }
            else
            {
                $this->wpvivid_logout_redirect();
            }
        }
        if( !isset( $_POST['log'] ) || !isset( $_POST['pwd'] ) )
        {

        }
        else
        {
            $user_data = get_user_by( 'login', $_POST['log'] );

            if( !$user_data ) {
                $user_data = get_user_by( 'email', $_POST['log'] );
            }

            if( $user_data )
            {
                if( wp_check_password( $_POST['pwd'], $user_data->user_pass, $user_data->ID ) )
                {

                    $rememberme = isset( $_POST['rememberme'] ) ? true : false;

                    wp_set_auth_cookie( $user_data->ID, $rememberme );
                    wp_set_current_user( $user_data->ID, $_POST['log'] );
                    do_action( 'wp_login', $_POST['log'], get_userdata( $user_data->ID ) );

                    $redirect_to = get_site_url() . '/wp-admin/';

                    if( !empty( $_POST['redirect_to'] ) ) {
                        $redirectTo = $_POST['redirect_to'];
                    }

                    header( 'Location:' . $redirectTo );
                }
            }
        }

        require_once( ABSPATH . 'wp-login.php' );

        ?>
        <script>
            jQuery(document).ready(function ()
            {
                jQuery('#loginform').prop('action', '');
            });
        </script>
        <?php

        die();
    }

    public function delete_site()
    {
        $this->ajax_check_security();
        try {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
            } else {
                die();
            }

            $ret = $this->_delete_site($id);

            $html = '';
            $list = get_option('wpvivid_staging_task_list', array());
            if (!empty($list)) {
                $display_list = new WPvivid_Staging_List_Ex();
                $display_list->set_parent('wpvivid_staging_list');
                $display_list->set_list($list);
                $display_list->prepare_items();
                ob_start();
                $display_list->display();
                $html = ob_get_clean();
            }
            $ret['html'] = $html;
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function wpvivid_get_staging_database_object($use_additional_db, $db_user, $db_pass, $db_name, $db_host){
        if($use_additional_db){
            return new wpdb($db_user, $db_pass, $db_name, $db_host);
        }
        else{
            global $wpdb;
            return $wpdb;
        }
    }

    public function delete_cancel_staging_site(){
        $this->ajax_check_security();
        try {
            if (isset($_POST['staging_site_info'])) {
                $json = $_POST['staging_site_info'];
                $json = stripslashes($json);
                $staging_site_info = json_decode($json, true);
                $site_path = $staging_site_info['staging_path'];
                $use_additional_db = $staging_site_info['staging_additional_db'];
                $db_user = $staging_site_info['staging_additional_db_user'];
                $db_pass = $staging_site_info['staging_additional_db_pass'];
                $db_name = $staging_site_info['staging_additional_db_name'];
                $db_host = $staging_site_info['staging_additional_db_host'];
                if (!empty($site_path)) {
                    $home_path = untrailingslashit(ABSPATH);
                    if ($home_path != $site_path) {
                        if (file_exists($site_path)) {
                            if (!class_exists('WP_Filesystem_Base')) include_once(ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php');
                            if (!class_exists('WP_Filesystem_Direct')) include_once(ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php');

                            $fs = new WP_Filesystem_Direct(false);
                            $fs->rmdir($site_path, true);
                        }
                    }
                }

                $prefix = $staging_site_info['staging_table_prefix'];
                if (!empty($prefix)) {
                    $db = $this->wpvivid_get_staging_database_object($use_additional_db, $db_user, $db_pass, $db_name, $db_host);
                    $sql = $db->prepare("SHOW TABLES LIKE %s;", $db->esc_like($prefix) . '%');
                    $result = $db->get_results($sql, OBJECT_K);

                    if (!empty($result)) {
                        foreach ($result as $table_name => $value) {
                            $table['name'] = $table_name;
                            $db->query("DROP TABLE IF EXISTS {$table_name}");
                        }
                    }
                }

                $ret['result'] = 'success';
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function _delete_site($site_id,$unfinished=false)
    {
        try
        {
            set_time_limit(900);
            $task=new WPvivid_Staging_Task_Ex($site_id);

            if($unfinished)
            {
                if($task->is_restore()||$task->is_copy())
                {
                    $ret['result']='success';
                    return $ret;
                }
                $site_path=$task->get_path(true);
                $prefix=$task->get_db_prefix(true);
                $copy_db=new WPvivid_Staging_Copy_DB_Ex($site_id);
                $db=$copy_db->get_db_instance(true);
            }
            else
            {
                $site_path=$task->get_site_path();
                $prefix=$task->get_site_prefix();
                $db=$task->get_site_db_instance();
            }

            if(empty($site_path))
            {
                $ret['result']='success';
                $default = array();
                $tasks = get_option('wpvivid_staging_task_list', $default);
                unset($tasks[$site_id]);
                update_option('wpvivid_staging_task_list',$tasks);
                return $ret;
            }

            $home_path=untrailingslashit(ABSPATH);
            if($home_path!=$site_path)
            {
                if (file_exists($site_path))
                {
                    if (!class_exists('WP_Filesystem_Base')) include_once(ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php');
                    if (!class_exists('WP_Filesystem_Direct')) include_once(ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php');

                    $fs = new WP_Filesystem_Direct(false);
                    $fs->rmdir($site_path, true);
                }
            }

            if(empty($prefix)||empty($db))
            {
                $ret['result']='success';
                $default = array();
                $tasks = get_option('wpvivid_staging_task_list', $default);
                unset($tasks[$site_id]);
                update_option('wpvivid_staging_task_list',$tasks);
                return $ret;
            }

            $sql=$db->prepare("SHOW TABLES LIKE %s;", $db->esc_like($prefix) . '%');
            $result = $db->get_results($sql, OBJECT_K);
            if(!empty($result))
            {
                $db->query( "SET foreign_key_checks = 0" );
                foreach ($result as $table_name=>$value)
                {
                    $table['name']=$table_name;
                    $db->query( "DROP TABLE IF EXISTS {$table_name}" );
                }
                $db->query( "SET foreign_key_checks = 1" );
            }

            $default = array();
            $tasks = get_option('wpvivid_staging_task_list', $default);
            unset($tasks[$site_id]);
            update_option('wpvivid_staging_task_list',$tasks);

            $ret['result']='success';
        }
        catch (Exception $error)
        {
            $ret['result']='failed';
            $ret['error']=$error->getMessage();
        }

        return $ret;
    }

    public function check_staging_dir()
    {
       $this->ajax_check_security();
        try {
            if (isset($_POST['path']) && !empty($_POST['path']) && is_string($_POST['path']) &&
                isset($_POST['table_prefix']) && !empty($_POST['table_prefix']) && is_string($_POST['table_prefix'])) {
                if (isset($_POST['root_dir']) && $_POST['root_dir'] == 1) {
                    $root = WP_CONTENT_DIR . DIRECTORY_SEPARATOR;
                } else {
                    $root = untrailingslashit(ABSPATH) . DIRECTORY_SEPARATOR;
                }
                $path = sanitize_text_field($_POST['path']);
                $path = $root . $path;
                $table_prefix = sanitize_text_field($_POST['table_prefix']);

                if (file_exists($path)) {
                    $ret['result'] = 'failed';
                    $ret['error'] = 'A folder with the same name already exists in website\'s root directory.';
                } else {
                    if (mkdir($path, 0755, true)) {
                        rmdir($path);
                    } else {
                        $ret['result'] = 'failed';
                        $ret['error'] = 'Create directory is not allowed in ' . $root . '.Please check the directory permissions and try again';
                    }
                }

                global $wpdb;
                $sql = $wpdb->prepare("SHOW TABLES LIKE %s;", $wpdb->esc_like($table_prefix) . '%');
                $result = $wpdb->get_results($sql, OBJECT_K);
                if (!empty($result)) {
                    $ret['result'] = 'failed';
                    $ret['error'] = 'The table prefix already exists.';
                }

                $ret['result'] = 'success';
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function output_staging()
    {
        $data=$this->get_staging_site_data();
        $data['live_site_staging_url'] = str_replace('wpvivid-staging', 'WPvivid_Staging', $data['live_site_staging_url']);
        $parent_url    = $data['parent_admin_url'];
        $live_site_url = $data['live_site_url'];
        $push_site_url = $data['live_site_staging_url'];
        ?>

        <div class="postbox quickbackup-addon">
            <table class="wp-list-table widefat plugins" style="width: 100%;">
                <tbody>
                <tr>
                    <td class="column-primary" style="border-bottom:1px solid #f1f1f1;background-color:#e2b300; color:#fff;" colspan="3">
                        <span><strong>Note: This is a staging site: </strong></span><span><?php echo _e(basename(get_home_path())); ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="column-primary" style="margin: 10px;">
                        <div>
                            <div style="margin:auto; width:100px; height:100px; right:50%;">
                                <img src="<?php echo esc_url(WPVIVID_STAGING_PLUGIN_URL.'includes/images/staging-site.png'); ?>">
                            </div>
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div>
                            <div style="height:20px;display:block;">The details of the staging enviroment</div>
                            <div style="height:20px;display:block;"><span>Database: </span><span><?php echo _e(DB_NAME); ?></span></div>
                            <div style="height:20px;display:block;"><span>Table Prefix: </span><span><?php echo _e($data['prefix']); ?></span></div>
                            <div style="height:20px;display:block;"><span>Site Directory: </span><span><?php echo _e(get_home_path()); ?></span></div>
                            <div style="height:20px;display:block;"><span>Live Site URL: </span><span><a href="<?php echo esc_url($live_site_url); ?>"><?php echo esc_url($live_site_url); ?></a></span></div>
                            <div style="height:20px;display:block;"><span>Live Site Staging: </span><span><a href="<?php echo esc_url($push_site_url); ?>"><?php echo esc_url($push_site_url); ?></a></span></div>
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div>
                            <div style="height:20px;display:block;margin-bottom:10px;text-align:center;">
                                <input class="button-primary" type="submit" name="post" value="Click here to migrate the staging site to live site" onclick="wpvivid_jump_live_staging();">
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <script>
            function wpvivid_jump_live_staging(){
                location.href='<?php echo $push_site_url; ?>';
            }
        </script>
        <!--<div id="staging_site">
            <?php $this->get_recent_post()?>
        </div>-->
        <?php
    }

    public function get_recent_post()
    {
        //set_prefix
        $post_type='post';
        $args = array(
            'orderby' => 'modified',
            'ignore_sticky_posts' => '1',
            'page_id' => 0,
            'posts_per_page' => 1,
            'post_type' => $post_type
        );

        $loop = new WP_Query( $args );
        $string = '<ul>';
        while( $loop->have_posts())
        {
            $loop->the_post();
            $string .= '<li><a href="' . get_permalink( $loop->post->ID ) . '"> ' .get_the_title( $loop->post->ID ) . '</a> ( '. get_the_modified_date() .') </li>';
        }
        $string .= '</ul>';
        $string.='<input id="wpvivid_update_post" type="button" class="button button-primary" value="Update">';
        echo $string;

    }

    public function update_recent_post()
    {
        $post_type='post';
        $args = array(
            'orderby' => 'modified',
            'ignore_sticky_posts' => '1',
            'page_id' => 0,
            'posts_per_page' => 1,
            'post_type' => $post_type
        );

        $loop = new WP_Query( $args );
        global $wpdb;

        $ret['result']='success';
        $posts=array();

        while( $loop->have_posts())
        {
            $post=$loop->next_post();
            $posts[$post->ID]['post']=$post;
            $posts[$post->ID]['postmeta']= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $post->ID ) );
        }


        $dbuser     = defined( 'DB_USER' ) ? DB_USER : '';
        $dbpassword = defined( 'DB_PASSWORD' ) ? DB_PASSWORD : '';
        $dbname     = 'test_post_import';
        $dbhost     = defined( 'DB_HOST' ) ? DB_HOST : '';

        $wpdb = new wpdb( $dbuser, $dbpassword, $dbname, $dbhost );
        $wpdb->set_prefix('wp_');

        foreach ($posts as $id=>$post)
        {
            $post_exists = post_exists( $post['post']->post_title, '', $post['post']->post_type );
            if ( $post_exists && get_post_type( $post_exists ) == $post['post']->post_type )
            {
                wp_update_post($post['post']);
                foreach ($post['postmeta'] as $meta)
                {
                    if(get_post_meta($id,$meta->meta_key,true))
                    {
                        update_post_meta( $id, $meta->meta_key ,  $meta->meta_value  );
                    }
                    else
                    {
                        add_post_meta( $id, $meta->meta_key ,  $meta->meta_value  );
                    }
                }
            }
            else
            {
                $postdata = array(
                    'import_id' => $post['post']->id, 'post_author' => $post['post']->post_author, 'post_date' => $post['post']->post_date,
                    'post_date_gmt' => $post['post']->post_date_gmt, 'post_content' => $post['post']->post_content,
                    'post_excerpt' => $post['post']->post_excerpt, 'post_title' => $post['post']->post_title,
                    'post_status' => $post['post']->post_status, 'post_name' => $post['post']->post_name,
                    'comment_status' =>  $post['post']->comment_status, 'ping_status' => $post['post']->ping_status,
                    'guid' => $post['post']->guid, 'post_parent' => $post['post']->post_parent, 'menu_order' => $post['post']->menu_order,
                    'post_type' => $post['post']->post_type, 'post_password' => $post['post']->post_password
                );
                $post_id = wp_insert_post( $postdata, true );
                foreach ($post['postmeta'] as $meta)
                {
                    if(get_post_meta($post_id,$meta->meta_key,true))
                    {
                        update_post_meta( $post_id, $meta->meta_key ,  $meta->meta_value  );
                    }
                    else
                    {
                        add_post_meta( $post_id, $meta->meta_key ,  $meta->meta_value  );
                    }
                }
            }

        }

        echo json_encode($ret);
        die();
    }

    public function get_staging_progress()
    {
        $task_id=get_option('wpvivid_current_running_staging_task','');
        if(empty($task_id))
        {
            $ret['result']='success';
            $ret['log']='';
            $ret['continue']=0;
            echo json_encode($ret);
            die();
        }

        try
        {
            $task=new WPvivid_Staging_Task_Ex($task_id);
            $b_delete=false;
            if($task->get_status()=='completed')
            {
                if($task->is_restore()){
                    $ret['completed_msg'] = 'Pushing the staging site to the live site completed successfully.';
                }
                else{
                    $ret['completed_msg'] = 'Updating the staging site completed successfully.';
                }
                update_option('wpvivid_current_running_staging_task','');
                $ret['continue']=0;
                $ret['completed']=1;
            }
            else if($task->get_status()=='ready')
            {
                $ret['continue']=1;
                $ret['need_restart']=1;
            }
            else if($task->get_status()=='error')
            {
                update_option('wpvivid_current_running_staging_task','');
                $ret['continue']=0;
                $ret['error']=1;
                $ret['error_msg']=$task->get_error();
                $b_delete=true;
            }
            else if($task->get_status()=='cancel')
            {
                update_option('wpvivid_current_running_staging_task','');
                $ret['continue']=0;
                $ret['need_restart']=0;
                $ret['is_cancel']=1;
                foreach ($task as $value){
                    $ret['staging_path']=$value['path']['des_path'];
                    if($value['db_connect']['des_use_additional_db']){
                        $ret['staging_additional_db']=1;
                        $ret['staging_additional_db_user']=$value['db_connect']['des_dbuser'];
                        $ret['staging_additional_db_pass']=$value['db_connect']['des_dbpassword'];
                        $ret['staging_additional_db_host']=$value['db_connect']['des_dbhost'];
                        $ret['staging_additional_db_name']=$value['db_connect']['des_dbname'];
                        $ret['staging_table_prefix']=$value['db_connect']['new_prefix'];
                    }
                    else{
                        $ret['staging_additional_db']=0;
                        $ret['staging_additional_db_user']=null;
                        $ret['staging_additional_db_pass']=null;
                        $ret['staging_additional_db_host']=null;
                        $ret['staging_additional_db_name']=null;
                        $ret['staging_table_prefix']=$value['db_connect']['new_prefix'];
                    }
                }
                update_option('wpvivid_staging_task_cancel', false);
                $b_delete=true;
            }
            else
            {
                if($task->check_timeout())
                {
                    if($task->get_status()=='ready')
                    {
                        $ret['continue']=1;
                        $ret['need_restart']=1;
                    }
                    else
                    {
                        update_option('wpvivid_current_running_staging_task','');
                        $ret['continue']=0;
                        $b_delete=true;
                    }
                }
                else
                {
                    $ret['continue']=1;
                    $ret['need_restart']=0;
                }
            }
            $staging_percent = $task->get_progress();
            $file_name=$this->log->GetSaveLogFolder(). $task->get_log_file_name().'_log.txt';
            $file =fopen($file_name,'r');
            $buffer='';
            if(!$file)
            {
                $buffer='open log file failed';
            }
            else
            {
                while(!feof($file))
                {
                    $buffer .= fread($file,1024);
                }
                fclose($file);
            }

            if($b_delete)
            {
                $this->_delete_site($task_id,true);
            }
            $ret['log']=$buffer;
            $ret['percent']=$staging_percent;
            $ret['result']='success';
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $ret['result']='failed';
            $ret['error']=$error->getMessage();
            echo json_encode($ret);
        }

        die();
    }

    public function cancel_staging()
    {
        $this->ajax_check_security();
        $task_id=get_option('wpvivid_current_running_staging_task','');
        if(empty($task_id))
        {
            $ret['result']='success';
            $ret['log']='';
            $ret['continue']=0;
            echo json_encode($ret);
            die();
        }

        try
        {
            $task=new WPvivid_Staging_Task_Ex($task_id);
            $task->cancel_staging();

            $ret['result']='success';
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $ret['result']='failed';
            $ret['error']=$error->getMessage();
            echo json_encode($ret);
        }
        die();
    }

    public function test_additional_database_connect(){
        $this->ajax_check_security();
        try {
            if (isset($_POST['database_info']) && !empty($_POST['database_info']) && is_string($_POST['database_info'])) {
                $data = $_POST['database_info'];
                $data = stripslashes($data);
                $json = json_decode($data, true);
                $db_user = sanitize_text_field($json['db_user']);
                $db_pass = sanitize_text_field($json['db_pass']);
                $db_host = sanitize_text_field($json['db_host']);
                $db_name = sanitize_text_field($json['db_name']);

                $db = new wpdb($db_user, $db_pass, $db_name, $db_host);
                // Can not connect to mysql
                if (!empty($db->error->errors['db_connect_fail']['0'])) {
                    $ret['result'] = 'failed';
                    $ret['error'] = 'Failed to connect to MySQL server. Please try again later.';
                    echo json_encode($ret);
                    die();
                }

                // Can not connect to database
                $db->select($db_name);
                if (!$db->ready) {
                    $ret['result'] = 'failed';
                    $ret['error'] = 'Unable to connect to MySQL database. Please try again later.';
                    echo json_encode($ret);
                    die();
                }
                $ret['result'] = 'success';

                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function update_staging_exclude_extension(){
        $this->ajax_check_security();
        try {
            if (isset($_POST['type']) && !empty($_POST['type']) && is_string($_POST['type']) &&
                isset($_POST['exclude_content']) && !empty($_POST['exclude_content']) && is_string($_POST['exclude_content'])) {
                $type = sanitize_text_field($_POST['type']);
                $value = sanitize_text_field($_POST['exclude_content']);

                $staging_option = self::wpvivid_get_staging_history();
                if (empty($staging_option)) {
                    $staging_option = array();
                }

                if ($type === 'upload') {
                    $staging_option['upload_extension'] = array();
                    $str_tmp = explode(',', $value);
                    for ($index = 0; $index < count($str_tmp); $index++) {
                        if (!empty($str_tmp[$index])) {
                            $staging_option['upload_extension'][] = $str_tmp[$index];
                        }
                    }
                } else if ($type === 'content') {
                    $staging_option['content_extension'] = array();
                    $str_tmp = explode(',', $value);
                    for ($index = 0; $index < count($str_tmp); $index++) {
                        if (!empty($str_tmp[$index])) {
                            $staging_option['content_extension'][] = $str_tmp[$index];
                        }
                    }
                } else if ($type === 'additional_file') {
                    $staging_option['additional_file_extension'] = array();
                    $str_tmp = explode(',', $value);
                    for ($index = 0; $index < count($str_tmp); $index++) {
                        if (!empty($str_tmp[$index])) {
                            $staging_option['additional_file_extension'][] = $str_tmp[$index];
                        }
                    }
                }

                self::wpvivid_set_staging_history($staging_option);

                $ret['result'] = 'success';
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function deal_shutdown_error($task_id)
    {
        if($this->end_shutdown_function===false)
        {
            $task=false;

            $last_error = error_get_last();
            if (!empty($last_error) && !in_array($last_error['type'], array(E_NOTICE,E_WARNING,E_USER_NOTICE,E_USER_WARNING,E_DEPRECATED), true))
            {
                $error = $last_error;
            } else {
                $error = false;
            }

            try
            {
                $task=new WPvivid_Staging_Task_Ex($task_id);
                if($error==false)
                {
                    $message='Create staging site end with a error.';
                }
                else
                {
                    $message=$error;
                }
                $task->finished_task_with_error($message);
                $this->log->WriteLog($message,'error');
            }
            catch (Exception $error)
            {
                $message = 'An Error has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
                error_log($message);
                if($task!==false)
                    $task->finished_task_with_error($message);
                $this->log->WriteLog($message,'error');
            }

            die();
        }
    }

    public function start_staging()
    {
        $this->end_shutdown_function=false;
        register_shutdown_function(array($this,'deal_staging_shutdown_error'));
        $task=false;
        try
        {
            $task_id=get_option('wpvivid_current_running_staging_task','');
            if(!empty($task_id))
            {
                $task=new WPvivid_Staging_Task_Ex($task_id);
                if($task->get_status()==='running')
                {
                    $this->end_shutdown_function=true;
                    die();
                }
                $this->log->OpenLogFile($task->get_log_file_name());
            }
            else
            {
                if(isset($_POST['path']) && isset($_POST['table_prefix']) && isset($_POST['custom_dir']) && isset($_POST['additional_db']))
                {
                    $json = $_POST['custom_dir'];
                    $json = stripslashes($json);
                    $staging_options = json_decode($json, true);

                    $additional_db_json = $_POST['additional_db'];
                    $additional_db_json = stripslashes($additional_db_json);
                    $additional_db_options = json_decode($additional_db_json, true);

                    $is_mu=$_POST['mu_quick_select'];

                    $is_mu_single=$_POST['mu_single_select'];

                    $option['options'] = $this->set_staging_option();

                    $src_path = untrailingslashit(ABSPATH);
                    $path = sanitize_text_field($_POST['path']);
                    if(isset($_POST['root_dir'])&&$_POST['root_dir']==1)
                    {
                        $url_path=str_replace(ABSPATH,'',WP_CONTENT_DIR).'/' . $path;
                        $des_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $path;
                    }
                    else
                    {
                        $url_path=$path;
                        $des_path = untrailingslashit(ABSPATH) . '/' . $path;
                    }

                    $option['data']['path']['src_path'] = $src_path;
                    $option['data']['path']['des_path'] = $des_path;

                    $table_prefix = $_POST['table_prefix'];

                    global $wpdb;

                    $option['data']['restore'] = false;
                    $option['data']['copy']=false;
                    if(is_multisite())
                    {
                        $option['data']['mu']['path_current_site']=PATH_CURRENT_SITE.$path.'/';
                        $subsites = get_sites();
                        foreach ($subsites as $subsite)
                        {
                            $subsite_id = get_object_vars($subsite)["blog_id"];
                            $str=get_object_vars($subsite)["path"];
                            $option['data']['mu']['site'][$subsite_id]['path_site']=$option['data']['mu']['path_current_site'].substr($str, strlen(PATH_CURRENT_SITE));
                            //$option['data']['mu']['site'][$subsite_id]['path_site'] = str_replace(PATH_CURRENT_SITE,PATH_CURRENT_SITE.$path.'/',get_object_vars($subsite)["path"]);
                            if(is_main_site($subsite_id))
                            {
                                $option['data']['mu']['main_site_id']=$subsite_id;
                            }
                        }
                    }

                    if( (is_string($is_mu)&&$is_mu=='true')||(is_bool($is_mu)&&$is_mu==true))
                    {
                        $this->set_mu_site_option($option,$staging_options,$additional_db_options,$url_path,$table_prefix);
                    }
                    else if( (is_string($is_mu_single)&&$is_mu_single=='true')||(is_bool($is_mu_single)&&$is_mu_single==true))
                    {
                        $this->set_mu_single_site_option($option,$staging_options,$additional_db_options,$url_path,$table_prefix);
                    }
                    else
                    {
                        $this->set_create_staging_option($option,$staging_options,$additional_db_options,$url_path,$table_prefix);
                    }

                    $task = new WPvivid_Staging_Task_Ex();
                    $task->set_memory_limit();
                    $task->setup_task($option);
                    $this->log->CreateLogFile($task->get_log_file_name(), 'no_folder', 'staging');
                    $this->log->WriteLog('Start creating staging site.', 'notice');
                    $this->log->WriteLogHander();
                }
            }

            $task_id=$task->get_id();
            update_option('wpvivid_current_running_staging_task',$task_id);
            register_shutdown_function(array($this,'deal_shutdown_error'),$task_id);

            $doing=$task->get_doing_task();
            if($doing===false)
            {
                $doing=$task->get_start_next_task();
            }

            $task->set_time_limit();
            if(!$task->do_task($doing))
            {
                $task->finished_task_with_error();
                $this->end_shutdown_function=true;
                die();
            }

            $doing=$task->get_start_next_task();
            if($doing==false)
            {
                $this->log->WriteLog('Creating staging site is completed.','notice');
                $task->finished_task();
            }
        }
        catch (Exception $error)
        {
            $message = 'An Error has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            error_log($message);
            if($task!==false)
                $task->finished_task_with_error($message);
            $this->log->WriteLog($message,'error');
        }

        $this->end_shutdown_function=true;
        die();
    }

    public function set_mu_site_option(&$option,$staging_options,$additional_db_options,$url_path,$table_prefix)
    {
        global $wpdb;
        $json = $_POST['mu_site_list'];
        $json = stripslashes($json);
        $mu_site_list_json = json_decode($json, true);
        //$mu_site_list=$mu_site_list['mu_site_list'];
        $mu_site_list=array();
        foreach ($mu_site_list_json['mu_site_list'] as $site)
        {
            $mu_site_list[$site['id']]['tables']=$site['tables'];
            $mu_site_list[$site['id']]['folders']=$site['folders'];
        }

        $subsites = get_sites();
        $mu_exclude_table=array();
        $mu_upload_exclude=array();
        //all_site
        if($mu_site_list_json['all_site'])
        {
        }
        else
        {
            foreach ($subsites as $subsite)
            {
                $subsite_id = get_object_vars($subsite)["blog_id"];
                if(array_key_exists($subsite_id,$mu_site_list))
                {
                    if($mu_site_list[$subsite_id]['tables']==0)
                    {
                        if(!is_main_site($subsite_id))
                        {
                            $prefix=$wpdb->get_blog_prefix($subsite_id);
                            $this->get_table_list($prefix,$mu_exclude_table);
                        }
                    }

                    if($mu_site_list[$subsite_id]['folders']==0)
                    {
                        if(!is_main_site($subsite_id))
                            $mu_upload_exclude[]=$this->get_upload_exclude_folder($subsite_id);
                    }
                }
                else
                {
                    $prefix=$wpdb->get_blog_prefix($subsite_id);
                    $this->get_table_list($prefix,$mu_exclude_table);
                    if(!is_main_site($subsite_id))
                        $mu_upload_exclude[]=$this->get_upload_exclude_folder($subsite_id);
                }
            }
        }

        $option['data']['db_connect']['old_prefix'] = $wpdb->base_prefix;
        $option['data']['db_connect']['old_site_url'] = untrailingslashit($this->get_database_site_url());
        $option['data']['db_connect']['old_home_url'] = untrailingslashit($this->get_database_home_url());
        $option['data']['db_connect']['new_site_url'] = $option['data']['db_connect']['old_site_url'] . '/' . $url_path;
        $option['data']['db_connect']['new_home_url'] = $option['data']['db_connect']['old_home_url'] . '/' . $url_path;
        $option['data']['db_connect']['src_use_additional_db'] = false;
        $option['data']['db_connect']['des_use_additional_db'] = false;
        $option['data']['db_connect']['new_prefix'] = $table_prefix;
        if(isset($additional_db_options['additional_database_check']) && $additional_db_options['additional_database_check'] == '1')
        {
            $option['data']['db_connect']['des_use_additional_db'] = true;
            $option['data']['db_connect']['des_dbuser'] = $additional_db_options['additional_database_info']['db_user'];
            $option['data']['db_connect']['des_dbpassword'] = $additional_db_options['additional_database_info']['db_pass'];
            $option['data']['db_connect']['des_dbname'] = $additional_db_options['additional_database_info']['db_name'];
            $option['data']['db_connect']['des_dbhost'] = $additional_db_options['additional_database_info']['db_host'];
        }
        $option['data']['db']['exclude_tables'] = array();
        $option['data']['db']['exclude_tables'][] =$wpdb->base_prefix.'hw_blocks';

        foreach ($mu_exclude_table as $table)
        {
            $option['data']['db']['exclude_tables'][] = $table;
        }

        $option['data']['theme']['exclude_regex'] = array();
        if($staging_options['themes_check'] == '1')
        {
            foreach ($staging_options['themes_list'] as $theme)
            {
                $option['data']['theme']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(get_theme_root().DIRECTORY_SEPARATOR.$theme), '/').'#';
            }
        }

        $option['data']['plugins']['exclude_regex'] = array();
        if($staging_options['plugins_check'] == '1')
        {
            foreach ($staging_options['plugins_list'] as $plugin)
            {
                $option['data']['plugins']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.$plugin), '/').'#';
            }
        }

        $option['data']['upload']['exclude_regex'] = array();
        $option['data']['upload']['exclude_files_regex']=array();
        foreach ($mu_upload_exclude as $value)
        {
            $option['data']['upload']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path($value), '/').'#';
        }
        if($staging_options['uploads_check'] == '1')
        {

            $upload_dir = wp_upload_dir();
            foreach ($staging_options['uploads_list'] as $key => $value)
            {
                $option['data']['upload']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path($upload_dir['basedir'].DIRECTORY_SEPARATOR.$key), '/').'#';
            }
            $upload_extension_tmp = array();
            if(isset($staging_options['upload_extension']) && !empty($staging_options['upload_extension']))
            {
                $str_tmp = explode(',', $staging_options['upload_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $option['data']['upload']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                        $upload_extension_tmp[] = $str_tmp[$index];
                    }
                }
                $staging_options['upload_extension'] = $upload_extension_tmp;
            }
        }

        $option['data']['wp-content']['exclude_regex'] = array();
        $option['data']['wp-content']['exclude_files_regex']=array();
        if($staging_options['content_check'] == '1')
        {
            $option['data']['wp-content']['exclude_regex'] = array();
            foreach ($staging_options['content_list'] as $key => $value)
            {
                $option['data']['wp-content']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$key), '/').'#';
            }
            $option['data']['wp-content']['exclude_files_regex']=array();
            $content_extension_tmp = array();
            if(isset($staging_options['content_extension']) && !empty($staging_options['content_extension']))
            {
                $str_tmp = explode(',', $staging_options['content_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $option['data']['wp-content']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                        $content_extension_tmp[] = $str_tmp[$index];
                    }
                }
                $staging_options['content_extension'] = $content_extension_tmp;
            }
        }

        $option['data']['core'] = true;

        if($staging_options['additional_file_check'] == '1')
        {
            $custom['exclude_regex'] = array();
            $custom['exclude_files_regex']=array();
            $additional_file_extension_tmp = array();
            if(isset($staging_options['additional_file_extension']) && !empty($staging_options['additional_file_extension']))
            {
                $str_tmp = explode(',', $staging_options['additional_file_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $custom['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                        $additional_file_extension_tmp[] = $str_tmp[$index];
                    }
                }
                $staging_options['additional_file_extension'] = $additional_file_extension_tmp;
            }
            foreach ($staging_options['additional_file_list'] as $key => $value)
            {
                $custom['root'] = $key;
                $option['data']['custom'][] = $custom;
            }
        }
    }

    public function set_mu_single_site_option(&$option,$staging_options,$additional_db_options,$url_path,$table_prefix)
    {
        global $wpdb;
        $json = $_POST['mu_single_site'];
        $json = stripslashes($json);
        $mu_site_list_json = json_decode($json, true);
        //$mu_site_list=$mu_site_list['mu_site_list'];
        $mu_single_site_id=$mu_site_list_json['id'];

        $subsites = get_sites();
        $mu_exclude_table=array();
        $mu_upload_exclude=array();
        foreach ($subsites as $subsite)
        {
            $subsite_id = get_object_vars($subsite)["blog_id"];
            if($mu_single_site_id==$subsite_id)
            {
                continue;
                /*
                if($mu_single_site_tables==0)
                {
                    if(!is_main_site($subsite_id))
                    {
                        $prefix=$wpdb->get_blog_prefix($subsite_id);
                        $this->get_table_list($prefix,$mu_exclude_table);
                    }
                }

                if($mu_single_site_folders==0)
                {
                    if(!is_main_site($subsite_id))
                        $mu_upload_exclude[]=$this->get_upload_exclude_folder($subsite_id);
                }*/
            }
            else
            {
                $prefix=$wpdb->get_blog_prefix($subsite_id);
                $this->get_table_list($prefix,$mu_exclude_table,false,false);
                if(!is_main_site($subsite_id))
                    $mu_upload_exclude[]=$this->get_upload_exclude_folder($subsite_id);
            }
        }

        $option['data']['mu_single']=true;
        $upload_path=$this->get_upload_exclude_folder($mu_single_site_id);
        $option['data']['mu_single_upload']=str_replace(ABSPATH,'',$upload_path);
        $option['data']['mu_single_site_id']=$mu_single_site_id;
        $prefix=$wpdb->get_blog_prefix($mu_single_site_id);

        $option['data']['db_connect']['old_prefix'] = $prefix;
        $option['data']['db_connect']['new_prefix'] = $table_prefix;
        $option['data']['db_connect']['old_site_url'] = get_site_url($mu_single_site_id);
        $option['data']['db_connect']['old_home_url'] = get_home_url($mu_single_site_id);
        //$option['data']['db_connect']['old_site_url'] = untrailingslashit($this->get_database_site_url());
        //        $option['data']['db_connect']['old_home_url'] = untrailingslashit($this->get_database_home_url());
        $option['data']['db_connect']['new_site_url'] = untrailingslashit($this->get_database_site_url()) . '/' . $url_path;
        $option['data']['db_connect']['new_home_url'] = untrailingslashit($this->get_database_home_url()) . '/' . $url_path;
        $option['data']['db_connect']['src_use_additional_db'] = false;
        $option['data']['db_connect']['des_use_additional_db'] = false;

        if(isset($additional_db_options['additional_database_check']) && $additional_db_options['additional_database_check'] == '1')
        {
            $option['data']['db_connect']['des_use_additional_db'] = true;
            $option['data']['db_connect']['des_dbuser'] = $additional_db_options['additional_database_info']['db_user'];
            $option['data']['db_connect']['des_dbpassword'] = $additional_db_options['additional_database_info']['db_pass'];
            $option['data']['db_connect']['des_dbname'] = $additional_db_options['additional_database_info']['db_name'];
            $option['data']['db_connect']['des_dbhost'] = $additional_db_options['additional_database_info']['db_host'];
        }
        $option['data']['db']['exclude_tables'] = array();
        $option['data']['db']['exclude_tables'][] =$wpdb->base_prefix.'hw_blocks';
        $option['data']['db']['exclude_tables'][] =$wpdb->base_prefix.'site';
        $option['data']['db']['exclude_tables'][] =$wpdb->base_prefix.'sitemeta';
        $option['data']['db']['exclude_tables'][] =$wpdb->base_prefix.'blogs';
        $option['data']['db']['exclude_tables'][] =$wpdb->base_prefix.'blogmeta';

        foreach ($mu_exclude_table as $table)
        {
            $option['data']['db']['exclude_tables'][] = $table;
        }

        $option['data']['theme']['exclude_regex'] = array();
        if($staging_options['themes_check'] == '1')
        {
            foreach ($staging_options['themes_list'] as $theme)
            {
                $option['data']['theme']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(get_theme_root().DIRECTORY_SEPARATOR.$theme), '/').'#';
            }
        }

        $option['data']['plugins']['exclude_regex'] = array();
        if($staging_options['plugins_check'] == '1')
        {
            foreach ($staging_options['plugins_list'] as $plugin)
            {
                $option['data']['plugins']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.$plugin), '/').'#';
            }
        }

        $option['data']['upload']['exclude_regex'] = array();
        $option['data']['upload']['exclude_files_regex']=array();
        foreach ($mu_upload_exclude as $value)
        {
            $option['data']['upload']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path($value), '/').'#';
        }
        if($staging_options['uploads_check'] == '1')
        {
            $upload_dir = wp_upload_dir();
            foreach ($staging_options['uploads_list'] as $key => $value)
            {
                $option['data']['upload']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path($upload_dir['basedir'].DIRECTORY_SEPARATOR.$key), '/').'#';
            }
            $upload_extension_tmp = array();
            if(isset($staging_options['upload_extension']) && !empty($staging_options['upload_extension']))
            {
                $str_tmp = explode(',', $staging_options['upload_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $option['data']['upload']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                        $upload_extension_tmp[] = $str_tmp[$index];
                    }
                }
                $staging_options['upload_extension'] = $upload_extension_tmp;
            }
        }

        $option['data']['wp-content']['exclude_regex'] = array();
        $option['data']['wp-content']['exclude_files_regex']=array();
        if($staging_options['content_check'] == '1')
        {
            $option['data']['wp-content']['exclude_regex'] = array();
            foreach ($staging_options['content_list'] as $key => $value)
            {
                $option['data']['wp-content']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$key), '/').'#';
            }
            $option['data']['wp-content']['exclude_files_regex']=array();
            $content_extension_tmp = array();
            if(isset($staging_options['content_extension']) && !empty($staging_options['content_extension']))
            {
                $str_tmp = explode(',', $staging_options['content_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $option['data']['wp-content']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                        $content_extension_tmp[] = $str_tmp[$index];
                    }
                }
                $staging_options['content_extension'] = $content_extension_tmp;
            }
        }

        $option['data']['core'] = true;

        if($staging_options['additional_file_check'] == '1')
        {
            $custom['exclude_regex'] = array();
            $custom['exclude_files_regex']=array();
            $additional_file_extension_tmp = array();
            if(isset($staging_options['additional_file_extension']) && !empty($staging_options['additional_file_extension']))
            {
                $str_tmp = explode(',', $staging_options['additional_file_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $custom['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                        $additional_file_extension_tmp[] = $str_tmp[$index];
                    }
                }
                $staging_options['additional_file_extension'] = $additional_file_extension_tmp;
            }
            foreach ($staging_options['additional_file_list'] as $key => $value)
            {
                $custom['root'] = $key;
                $option['data']['custom'][] = $custom;
            }
        }
    }

    public function set_create_staging_option(&$option,$staging_options,$additional_db_options,$url_path,$table_prefix)
    {
        global $wpdb;

        if(isset($_POST['create_new_wp']))
        {
            $option['data']['core'] = true;

            if($staging_options['themes_check'] == '1')
            {
                $option['data']['theme']['exclude_regex'] = array();
                foreach ($staging_options['themes_list'] as $theme)
                {
                    $option['data']['theme']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(get_theme_root().DIRECTORY_SEPARATOR.$theme), '/').'#';
                }
            }

            if($staging_options['plugins_check'] == '1')
            {
                $option['data']['plugins']['exclude_regex'] = array();
                foreach ($staging_options['plugins_list'] as $plugin)
                {
                    $option['data']['plugins']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.$plugin), '/').'#';
                }
            }

            $option['data']['db_connect']['old_prefix'] = $wpdb->base_prefix;
            $option['data']['db_connect']['old_site_url'] = untrailingslashit($this->get_database_site_url());
            $option['data']['db_connect']['old_home_url'] = untrailingslashit($this->get_database_home_url());
            $option['data']['db_connect']['new_site_url'] = $option['data']['db_connect']['old_site_url'] . '/' . $url_path;
            $option['data']['db_connect']['new_home_url'] = $option['data']['db_connect']['old_home_url'] . '/' . $url_path;
            $option['data']['db_connect']['src_use_additional_db'] = false;
            $option['data']['db_connect']['des_use_additional_db'] = false;
            $option['data']['db_connect']['new_prefix'] = $table_prefix;
            if(isset($additional_db_options['additional_database_check']) && $additional_db_options['additional_database_check'] == '1')
            {
                $option['data']['db_connect']['des_use_additional_db'] = true;
                $option['data']['db_connect']['des_dbuser'] = $additional_db_options['additional_database_info']['db_user'];
                $option['data']['db_connect']['des_dbpassword'] = $additional_db_options['additional_database_info']['db_pass'];
                $option['data']['db_connect']['des_dbname'] = $additional_db_options['additional_database_info']['db_name'];
                $option['data']['db_connect']['des_dbhost'] = $additional_db_options['additional_database_info']['db_host'];
            }

            $option['data']['create_new_wp']=true;
            //$option['data']['db']['exclude_tables'] = array();
            //$option['data']['db']['exclude_tables'][] = $wpdb->base_prefix.'hw_blocks';
            //foreach ($staging_options['database_list'] as $table)
            //{
            //    $option['data']['db']['exclude_tables'][] = $table;
            //}
        }
        else
        {
            if($staging_options['database_check'] == '1')
            {
                $option['data']['db_connect']['old_prefix'] = $wpdb->base_prefix;
                $option['data']['db_connect']['old_site_url'] = untrailingslashit($this->get_database_site_url());
                $option['data']['db_connect']['old_home_url'] = untrailingslashit($this->get_database_home_url());
                $option['data']['db_connect']['new_site_url'] = $option['data']['db_connect']['old_site_url'] . '/' . $url_path;
                $option['data']['db_connect']['new_home_url'] = $option['data']['db_connect']['old_home_url'] . '/' . $url_path;
                $option['data']['db_connect']['src_use_additional_db'] = false;
                $option['data']['db_connect']['des_use_additional_db'] = false;
                $option['data']['db_connect']['new_prefix'] = $table_prefix;
                if(isset($additional_db_options['additional_database_check']) && $additional_db_options['additional_database_check'] == '1')
                {
                    $option['data']['db_connect']['des_use_additional_db'] = true;
                    $option['data']['db_connect']['des_dbuser'] = $additional_db_options['additional_database_info']['db_user'];
                    $option['data']['db_connect']['des_dbpassword'] = $additional_db_options['additional_database_info']['db_pass'];
                    $option['data']['db_connect']['des_dbname'] = $additional_db_options['additional_database_info']['db_name'];
                    $option['data']['db_connect']['des_dbhost'] = $additional_db_options['additional_database_info']['db_host'];
                }
                $option['data']['db']['exclude_tables'] = array();
                $option['data']['db']['exclude_tables'][] = $wpdb->base_prefix.'hw_blocks';
                foreach ($staging_options['database_list'] as $table)
                {
                    $option['data']['db']['exclude_tables'][] = $table;
                }
            }

            $option['data']['theme']['exclude_regex'] = array();
            if($staging_options['themes_check'] == '1')
            {
                foreach ($staging_options['themes_list'] as $theme)
                {
                    $option['data']['theme']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(get_theme_root().DIRECTORY_SEPARATOR.$theme), '/').'#';
                }
            }

            $option['data']['plugins']['exclude_regex'] = array();
            if($staging_options['plugins_check'] == '1')
            {
                foreach ($staging_options['plugins_list'] as $plugin)
                {
                    $option['data']['plugins']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.$plugin), '/').'#';
                }
            }

            if($staging_options['uploads_check'] == '1')
            {
                $upload_dir = wp_upload_dir();
                $option['data']['upload']['exclude_regex'] = array();
                foreach ($staging_options['uploads_list'] as $key => $value)
                {
                    $option['data']['upload']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path($upload_dir['basedir'].DIRECTORY_SEPARATOR.$key), '/').'#';
                }
                $option['data']['upload']['exclude_files_regex']=array();
                $upload_extension_tmp = array();
                if(isset($staging_options['upload_extension']) && !empty($staging_options['upload_extension']))
                {
                    $str_tmp = explode(',', $staging_options['upload_extension']);
                    for($index=0; $index<count($str_tmp); $index++)
                    {
                        if(!empty($str_tmp[$index]))
                        {
                            $option['data']['upload']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                            $upload_extension_tmp[] = $str_tmp[$index];
                        }
                    }
                    $staging_options['upload_extension'] = $upload_extension_tmp;
                }
            }

            if($staging_options['content_check'] == '1')
            {
                $option['data']['wp-content']['exclude_regex'] = array();
                foreach ($staging_options['content_list'] as $key => $value)
                {
                    $option['data']['wp-content']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$key), '/').'#';
                }
                $option['data']['wp-content']['exclude_files_regex']=array();
                $content_extension_tmp = array();
                if(isset($staging_options['content_extension']) && !empty($staging_options['content_extension']))
                {
                    $str_tmp = explode(',', $staging_options['content_extension']);
                    for($index=0; $index<count($str_tmp); $index++)
                    {
                        if(!empty($str_tmp[$index]))
                        {
                            $option['data']['wp-content']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                            $content_extension_tmp[] = $str_tmp[$index];
                        }
                    }
                    $staging_options['content_extension'] = $content_extension_tmp;
                }
            }

            if($staging_options['core_check'] == '1')
            {
                $option['data']['core'] = true;
            }

            if($staging_options['additional_file_check'] == '1')
            {
                $custom['exclude_regex'] = array();
                $custom['exclude_files_regex']=array();
                $additional_file_extension_tmp = array();
                if(isset($staging_options['additional_file_extension']) && !empty($staging_options['additional_file_extension']))
                {
                    $str_tmp = explode(',', $staging_options['additional_file_extension']);
                    for($index=0; $index<count($str_tmp); $index++)
                    {
                        if(!empty($str_tmp[$index]))
                        {
                            $custom['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                            $additional_file_extension_tmp[] = $str_tmp[$index];
                        }
                    }
                    $staging_options['additional_file_extension'] = $additional_file_extension_tmp;
                }
                foreach ($staging_options['additional_file_list'] as $key => $value)
                {
                    $custom['root'] = $key;
                    $option['data']['custom'][] = $custom;
                }
            }

            self::wpvivid_set_staging_history($staging_options);
        }
    }

    public function get_table_list($prefix,&$mu_exclude_table,$task=false,$exculude_user=true)
    {

        global $wpdb;

        if($task===false)
        {
            $db=$wpdb;
        }
        else
        {
            $db=$task->get_site_db_instance();
        }

        $sql=$db->prepare("SHOW TABLES LIKE %s;", $wpdb->esc_like($prefix) . '%');
        $result = $db->get_results($sql, OBJECT_K);
        foreach ($result as $table_name=>$value)
        {
            if($prefix==$db->base_prefix)
            {
                if ( 1 == preg_match('/^' . $db->base_prefix . '\d+_/', $table_name) )
                {

                }
                else
                {
                    if($table_name==$db->base_prefix.'blogs'&&$exculude_user!==false)
                        continue;
                    if($exculude_user===false)
                    {
                        if($table_name==$db->base_prefix.'users'||$table_name==$db->base_prefix.'usermeta')
                            continue;
                    }
                    $mu_exclude_table[]=$table_name;
                }
            }
            else
            {
                $mu_exclude_table[]=$table_name;
            }
        }
    }

    public function get_upload_exclude_folder($site_id,$des=false,$task=false)
    {
        if($des)
        {
            $upload_dir = wp_upload_dir();
            $dir = str_replace( ABSPATH, '', $upload_dir['basedir'] );
            $src_path=$task->get_site_path();
            $upload_basedir=$src_path.DIRECTORY_SEPARATOR.$dir;
            if ( defined( 'MULTISITE' ) )
            {
                $upload_basedir = $upload_basedir.'/sites/' . $site_id;
            } else {
                $upload_basedir = $upload_basedir.'/' . $site_id;
            }
            return $upload_basedir;
        }
        else
        {
            $upload= $this->get_site_upload_dir($site_id);
            return $upload['basedir'];
        }
    }

    public function get_site_upload_dir($site_id, $time = null, $create_dir = true, $refresh_cache = false)
    {
        static $cache = array(), $tested_paths = array();

        $key = sprintf( '%d-%s',$site_id, (string) $time );

        if ( $refresh_cache || empty( $cache[ $key ] ) ) {
            $cache[ $key ] = $this->_wp_upload_dir( $site_id,$time );
        }

        /**
         * Filters the uploads directory data.
         *
         * @since 2.0.0
         *
         * @param array $uploads Array of upload directory data with keys of 'path',
         *                       'url', 'subdir, 'basedir', and 'error'.
         */
        $uploads = apply_filters( 'upload_dir', $cache[ $key ] );

        if ( $create_dir ) {
            $path = $uploads['path'];

            if ( array_key_exists( $path, $tested_paths ) ) {
                $uploads['error'] = $tested_paths[ $path ];
            } else {
                if ( ! wp_mkdir_p( $path ) ) {
                    if ( 0 === strpos( $uploads['basedir'], ABSPATH ) ) {
                        $error_path = str_replace( ABSPATH, '', $uploads['basedir'] ) . $uploads['subdir'];
                    } else {
                        $error_path = basename( $uploads['basedir'] ) . $uploads['subdir'];
                    }

                    $uploads['error'] = sprintf(
                    /* translators: %s: directory path */
                        __( 'Unable to create directory %s. Is its parent directory writable by the server?' ),
                        esc_html( $error_path )
                    );
                }

                $tested_paths[ $path ] = $uploads['error'];
            }
        }

        return $uploads;
    }

    public function _wp_upload_dir($site_id, $time = null ) {
        $siteurl     = get_option( 'siteurl' );
        $upload_path = trim( get_option( 'upload_path' ) );

        if ( empty( $upload_path ) || 'wp-content/uploads' == $upload_path ) {
            $dir = WP_CONTENT_DIR . '/uploads';
        } elseif ( 0 !== strpos( $upload_path, ABSPATH ) ) {
            // $dir is absolute, $upload_path is (maybe) relative to ABSPATH
            $dir = path_join( ABSPATH, $upload_path );
        } else {
            $dir = $upload_path;
        }

        if ( ! $url = get_option( 'upload_url_path' ) ) {
            if ( empty( $upload_path ) || ( 'wp-content/uploads' == $upload_path ) || ( $upload_path == $dir ) ) {
                $url = WP_CONTENT_URL . '/uploads';
            } else {
                $url = trailingslashit( $siteurl ) . $upload_path;
            }
        }

        /*
         * Honor the value of UPLOADS. This happens as long as ms-files rewriting is disabled.
         * We also sometimes obey UPLOADS when rewriting is enabled -- see the next block.
         */
        if ( defined( 'UPLOADS' ) && ! ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) ) {
            $dir = ABSPATH . UPLOADS;
            $url = trailingslashit( $siteurl ) . UPLOADS;
        }

        // If multisite (and if not the main site in a post-MU network)
        if ( is_multisite() && ! ( is_main_network() && is_main_site($site_id) && defined( 'MULTISITE' ) ) ) {
            if ( ! get_site_option( 'ms_files_rewriting' ) ) {
                /*
                 * If ms-files rewriting is disabled (networks created post-3.5), it is fairly
                 * straightforward: Append sites/%d if we're not on the main site (for post-MU
                 * networks). (The extra directory prevents a four-digit ID from conflicting with
                 * a year-based directory for the main site. But if a MU-era network has disabled
                 * ms-files rewriting manually, they don't need the extra directory, as they never
                 * had wp-content/uploads for the main site.)
                 */

                if ( defined( 'MULTISITE' ) ) {
                    $ms_dir = '/sites/' . $site_id;
                } else {
                    $ms_dir = '/' . $site_id;
                }

                $dir .= $ms_dir;
                $url .= $ms_dir;
            } elseif ( defined( 'UPLOADS' ) && ! ms_is_switched() ) {
                /*
                 * Handle the old-form ms-files.php rewriting if the network still has that enabled.
                 * When ms-files rewriting is enabled, then we only listen to UPLOADS when:
                 * 1) We are not on the main site in a post-MU network, as wp-content/uploads is used
                 *    there, and
                 * 2) We are not switched, as ms_upload_constants() hardcodes these constants to reflect
                 *    the original blog ID.
                 *
                 * Rather than UPLOADS, we actually use BLOGUPLOADDIR if it is set, as it is absolute.
                 * (And it will be set, see ms_upload_constants().) Otherwise, UPLOADS can be used, as
                 * as it is relative to ABSPATH. For the final piece: when UPLOADS is used with ms-files
                 * rewriting in multisite, the resulting URL is /files. (#WP22702 for background.)
                 */

                if ( defined( 'BLOGUPLOADDIR' ) ) {
                    $dir = untrailingslashit( BLOGUPLOADDIR );
                } else {
                    $dir = ABSPATH . UPLOADS;
                }
                $url = trailingslashit( $siteurl ) . 'files';
            }
        }

        $basedir = $dir;
        $baseurl = $url;

        $subdir = '';
        if ( get_option( 'uploads_use_yearmonth_folders' ) ) {
            // Generate the yearly and monthly dirs
            if ( ! $time ) {
                $time = current_time( 'mysql' );
            }
            $y      = substr( $time, 0, 4 );
            $m      = substr( $time, 5, 2 );
            $subdir = "/$y/$m";
        }

        $dir .= $subdir;
        $url .= $subdir;

        return array(
            'path'    => $dir,
            'url'     => $url,
            'subdir'  => $subdir,
            'basedir' => $basedir,
            'baseurl' => $baseurl,
            'error'   => false,
        );
    }

    public function deal_staging_shutdown_error()
    {
        if($this->end_shutdown_function===false)
        {
            $last_error = error_get_last();
            if (!empty($last_error) && !in_array($last_error['type'], array(E_NOTICE,E_WARNING,E_USER_NOTICE,E_USER_WARNING,E_DEPRECATED), true))
            {
                $error = $last_error;
            } else {
                $error = false;
            }

            if ($error === false)
            {
                $error = 'unknown Error';
            } else {
                $error = 'type: '. $error['type'] . ', ' . $error['message'] . ' file:' . $error['file'] . ' line:' . $error['line'];
                error_log($error);
            }
            $this->log->WriteLog($error,'error');

            die();
        }
    }

    public static function wpvivid_set_staging_history($option){
        update_option('wpvivid_staging_history', $option);
    }

    public static function wpvivid_get_staging_history(){
        $options = get_option('wpvivid_staging_history', array());
        return $options;
    }

    public static function wpvivid_set_push_staging_history($option){
        update_option('wpvivid_push_staging_history', $option);
    }

    public static function wpvivid_get_push_staging_history(){
        $options = get_option('wpvivid_push_staging_history', array());
        return $options;
    }

    private function transfer_path($path)
    {
        $path = str_replace('\\','/',$path);
        $values = explode('/',$path);
        return implode(DIRECTORY_SEPARATOR,$values);
    }

    public function set_staging_option()
    {
        $options=get_option('wpvivid_staging_options');

        if(isset($options['staging_db_insert_count']))
            $option['staging_db_insert_count']=$options['staging_db_insert_count'];
        else
            $option['staging_db_insert_count']=WPVIVID_STAGING_DB_INSERT_COUNT_EX;

        if(isset($options['staging_db_replace_count']))
            $option['staging_db_replace_count']=$options['staging_db_replace_count'];
        else
            $option['staging_db_replace_count']=WPVIVID_STAGING_DB_REPLACE_COUNT_EX;

        if(isset($options['staging_memory_limit']))
            $option['staging_memory_limit']=$options['staging_memory_limit'];
        else
            $option['staging_memory_limit']=WPVIVID_STAGING_MEMORY_LIMIT_EX;

        if(isset($options['staging_file_copy_count']))
            $option['staging_file_copy_count']=$options['staging_file_copy_count'];
        else
            $option['staging_file_copy_count']=WPVIVID_STAGING_FILE_COPY_COUNT_EX;

        if(isset($options['staging_exclude_file_size'])) {
            $option['staging_exclude_file_size'] = $options['staging_exclude_file_size'];
        }
        else {
            $option['staging_exclude_file_size'] = WPVIVID_STAGING_MAX_FILE_SIZE_EX;
        }

        if(isset($options['staging_max_execution_time']))
            $option['staging_max_execution_time']=$options['staging_max_execution_time'];
        else
            $option['staging_max_execution_time']=WPVIVID_STAGING_MAX_EXECUTION_TIME_EX;

        if(isset($options['staging_resume_count']))
            $option['staging_resume_count']=$options['staging_resume_count'];
        else
            $option['staging_resume_count']=WPVIVID_STAGING_RESUME_COUNT_EX;

        if(isset($options['staging_overwrite_permalink']))
            $option['staging_overwrite_permalink']=$options['staging_overwrite_permalink'];
        else
            $option['staging_overwrite_permalink']=0;

        return $option;
    }

    public function push_site()
    {
        $this->ajax_check_security();
        try
        {
            $task_id = $_POST['id'];
            if (!empty($task_id))
            {
                $task = new WPvivid_Staging_Task_Ex($task_id);
                $home_path = $task->get_site_path();
                $home_path = untrailingslashit($home_path);
                $home_path = str_replace('\\', '/', $home_path);

                $ret['result'] = 'success';
                $ret['home_path'] = $home_path . '/';
                $ret['uploads_path'] = $home_path . '/wp-content/uploads/';
                $ret['content_path'] = $home_path . '/wp-content/';
            } else {
                $ret['result'] = 'failed';
                $ret['error'] = 'not found site';
            }
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function copy_site()
    {
        $this->ajax_check_security();
        try
        {
            $task_id = $_POST['id'];
            if (!empty($task_id))
            {
                //$task = new WPvivid_Staging_Task_Ex($task_id);
                //$home_path = $task->get_site_path();
                //$home_path = untrailingslashit($home_path);
                //$home_path = str_replace('\\', '/', $home_path);

                $upload_dir = wp_upload_dir();
                $upload_path = $upload_dir['basedir'];
                $upload_path = str_replace('\\','/',$upload_path);
                $upload_path = $upload_path.'/';
                $content_dir = WP_CONTENT_DIR;
                $content_path = str_replace('\\','/',$content_dir);
                $content_path = $content_path.'/';
                $home_path = str_replace('\\','/', get_home_path());

                $ret['result'] = 'success';
                $ret['home_path'] = $home_path;
                $ret['uploads_path'] = $upload_path ;
                $ret['content_path'] = $content_path;
            } else {
                $ret['result'] = 'failed';
                $ret['error'] = 'not found site';
            }
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function get_mu_site_info()
    {
        $this->ajax_check_security();
        try
        {
            $task_id = $_POST['id'];
            if (!empty($task_id))
            {
                global $wpdb;
                $old_db=$wpdb;
                $task = new WPvivid_Staging_Task_Ex($task_id);
                $html='';

                if(is_multisite())
                {
                    if($_POST['copy']=='true')
                    {
                        $subsites=$task->get_mu_sites();
                        $is_restore=true;
                        $task = new WPvivid_Staging_Task_Ex($task_id);
                        $home_path = $task->get_site_path();
                        $home_path = untrailingslashit($home_path);
                        $home_path = str_replace('\\', '/', $home_path);
                        $ret['home_path'] = $home_path . '/';
                        $ret['uploads_path'] = $home_path . '/wp-content/uploads/';
                        $ret['content_path'] = $home_path . '/wp-content/';
                    }
                    else
                    {
                        $subsites=get_sites();
                        $is_restore=false;
                        $upload_dir = wp_upload_dir();
                        $upload_path = $upload_dir['basedir'];
                        $upload_path = str_replace('\\','/',$upload_path);
                        $upload_path = $upload_path.'/';
                        $content_dir = WP_CONTENT_DIR;
                        $content_path = str_replace('\\','/',$content_dir);
                        $content_path = $content_path.'/';
                        $home_path = str_replace('\\','/', get_home_path());
                        $ret['home_path'] = $home_path;
                        $ret['uploads_path'] = $upload_path ;
                        $ret['content_path'] = $content_path;
                    }
                    $list=array();
                    $main_site_id='0';
                    $main_site_name='';
                    $main_site_title=get_option( 'blogname' );
                    $main_site_description=get_option( 'blogdescription' );

                    foreach ($subsites as $subsite)
                    {
                        if($_POST['copy']=='true')
                        {
                            if($task->get_mu_main_site_id()==get_object_vars($subsite)["blog_id"])
                            {
                                $main_site_id=get_object_vars($subsite)["blog_id"];
                                $main_site_name = get_object_vars($subsite)["domain"].get_object_vars($subsite)["path"];
                            }
                            else
                            {
                                $list[]=$subsite;
                            }
                        }
                        else
                        {
                            if(is_main_site(get_object_vars($subsite)["blog_id"]))
                            {
                                $main_site_id=get_object_vars($subsite)["blog_id"];
                                $main_site_name = get_object_vars($subsite)["domain"].get_object_vars($subsite)["path"];
                            }
                            else
                            {
                                $list[]=$subsite;
                            }
                        }
                    }


                    $html.='';
                    if($task->get_site_mu_single())
                    {

                    }
                    else
                    {
                        $html.='<div style="padding:10px; background: #fff; border: 1px solid #ccd0d4; border-radius: 6px; margin-top: 10px;">';
                    }

                    if($is_restore)
                    {
                        $core_descript = 'If the staging site and the live site have the same version of WordPress. Then it is not necessary to copy the WordPress MU core files to the live site';
                        $db_descript = 'All the tables in the WordPress MU database except for subsites tables.';
                        $themes_plugins_descript = 'All the plugins and themes files used by the MU network. The activated plugins and themes will be copied to the live site by default. A child theme must be copied if it exists.';
                        $uploads_descript = 'The folder where images and media files of the main site are stored by default. All files will be copied to the live site by default. You can exclude folders you do not want to copy.';
                        $contents_descript = '<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to copy to the live site, except for the wp-content/uploads folder.';
                        $additional_file_descript = '<strong style="text-decoration:underline;"><i>Include</i></strong> additional files or folders you want to copy to the live site';
                        $select_subsite = 'Select the subsites you wish to copy to the live site';
                    }
                    else {
                        $core_descript = 'If the staging site and the live site have the same version of WordPress. Then it is not necessary to update the WordPress MU core files to the staging site.';
                        $db_descript = 'All the tables in the WordPress MU database except for subsites tables.';
                        $themes_plugins_descript = 'All the plugins and themes files used by the MU network. The activated plugins and themes will be updated to the staging site by default. A child theme must be updated if it exists.';
                        $uploads_descript = 'The folder where images and media files of the main site are stored by default. All files will be updated to the staging site by default. You can exclude folders you do not want to update.';
                        $contents_descript = '<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to update to the staging site, except for the wp-content/uploads folder.';
                        $additional_file_descript = '<strong style="text-decoration:underline;"><i>Include</i></strong> additional files or folders you want to updated to the staging site.';
                        $select_subsite = 'Select the subsites you wish to update to the staging site';
                    }

                    if($task->get_site_mu_single())
                    {

                    }
                    else
                    {
                        $html.= ' <label class="wpvivid-element-space-bottom" style="width:100%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap; padding-top: 3px;">
                        <input id="wpvivid_mu_main_site_check" type="checkbox" option="wpvividstg_mu_sites" name="copy_mu_site_main" value="'.$main_site_id.'" checked/>
                        MU Files and Database
                        </label>';
                    }

                    $html.='<div id="wpvivid_custom_mu_staging_site" class="wpvivid-element-space-bottom">
                        <table id="wpvivid_mu_main_site_check_table" class="wp-list-table widefat plugins wpvivid-custom-table">
                            <tbody>       
                            <tr>
                                <th class="check-column" scope="row" style="padding-left: 6px;">
                                    <label class="screen-reader-text" for=""></label>
                                    <input type="checkbox" name="copy_mu_site_main_core" checked/>
                                </th>
                                <td class="plugin-title column-primary wpvivid-backup-to-font">WordPress MU Core</td>
                                <td class="column-description desc">'.$core_descript.'</td>
                            </tr>                              
                            <tr style="cursor:pointer;">
                                <th class="check-column" scope="row" style="padding-left: 6px;">
                                    <label class="screen-reader-text" for=""></label>
                                    <input type="checkbox" name="copy_mu_site_main_tables" checked/>
                                </th>
                                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-database-detail">Database</td>
                                <td class="column-description desc wpvivid-handle-database-detail">
                                    '.$db_descript.'
                                </td>
                            </tr>
                            <tr style="cursor:pointer">
                                <th class="check-column" scope="row" style="padding-left: 6px;">
                                    <label class="screen-reader-text" for=""></label>
                                    <input type="checkbox" name="copy_mu_site_main_folders" checked/>
                                </th>
                                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-uploads-detail">wp-content/uploads</td>
                                <td class="column-description desc wpvivid-handle-uploads-detail">'.$uploads_descript.'</td>
                                <th class="wpvivid-handle-uploads-detail">
                                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                                    </details>
                                </th>
                            </tr>          
                            <tr class="wpvivid-custom-detail wpvivid-uploads-detail wpvivid-close" style="pointer-events: auto; opacity: 1; display: none;">
                                <th class="check-column"></th>
                                <td colspan="3" class="plugin-title column-primary">
                                    <table class="wp-list-table widefat plugins" style="width:100%;">
                                        <thead>
                                        <tr>
                                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                                <label class="wpvivid-refresh-tree wpvivid-refresh-uploads-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder Tree</label>
                                            </th>
                                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                                <div class="wpvivid-custom-uploads-tree">
                                                    <div class="wpvivid-custom-tree wpvivid-custom-uploads-tree-info"></div>
                                                </div>
                                            </td>
                                            <td class="wpvivid-custom-uploads-right">
                                                <div class="wpvivid-custom-uploads-table wpvivid-custom-exclude-uploads-list">
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="2">
                                                <div>
                                                    <div style="float: left; margin-right: 10px;">
                                                        <input class="button-primary wpvivid-exclude-uploads-folder-btn" type="submit" value="Exclude Folders" disabled />
                                                    </div>
                                                    <small>
                                                        <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                                            <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                                        </div>
                                                    </small>
                                                    <div style="clear: both;"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tfoot>
                                        <div style="clear:both;"></div>
                                    </table>
                                    <div style="margin-top: 10px;">
                                        <div style="float: left; margin-right: 10px;">
                                            <input type="text" class="regular-text wpvivid-uploads-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,\'\')"/>
                                            <input type="button" class="wpvivid-uploads-extension-rule-btn" value="Save" />
                                        </div>
                                        <small>
                                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                                            </div>
                                        </small>
                                        <div style="clear: both;"></div>
                                    </div>
                                </td>
                            </tr>               
                            <tr style="cursor:pointer">
                                <th class="check-column" scope="row" style="padding-left: 6px;">
                                    <label class="screen-reader-text" for=""></label>
                                    <input type="checkbox" name="copy_mu_site_main_themes_plugins" checked/>
                                </th>
                                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-themes-plugins-detail">Themes and Plugins</td>
                                <td class="column-description desc wpvivid-handle-themes-plugins-detail">
                                    '.$themes_plugins_descript.'
                                </td>
                                <th class="wpvivid-handle-themes-plugins-detail">
                                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                                    </details>
                                </th>
                            </tr>
                            <tr class="wpvivid-custom-detail wpvivid-themes-plugins-detail wpvivid-close" style="pointer-events: auto; opacity: 1; display: none;">
                                <th class="check-column"></th>
                                <td colspan="3" class="plugin-title column-primary wpvivid-custom-themes-plugins-info">
                                    <div class="spinner" style="margin: 0 5px 10px 0; float: left;"></div>
                                    <div style="float: left;">Archieving themes and plugins</div>
                                    <div style="clear: both;"></div>
                                </td>
                            </tr>
                            <tr style="cursor:pointer">
                                <th class="check-column" scope="row" style="padding-left: 6px;">
                                    <label class="screen-reader-text" for=""></label>
                                    <input type="checkbox" name="copy_mu_site_main_content" class="wpvivid-custom-check wpvivid-custom-content-check" checked/>
                                </th>
                                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-content-detail">wp-content</td>
                                <td class="column-description desc wpvivid-handle-content-detail"> '.$contents_descript.'</td>
                                <th class="wpvivid-handle-content-detail">
                                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                                    </details>
                                </th>
                            </tr>
                            <tr class="wpvivid-custom-detail wpvivid-content-detail wpvivid-close" style="pointer-events: auto; opacity: 1; display: none;">
                                <th class="check-column"></th>
                                <td colspan="3" class="plugin-title column-primary">
                                    <table class="wp-list-table widefat plugins" style="width:100%;">
                                        <thead>
                                        <tr>
                                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                                <label class="wpvivid-refresh-tree wpvivid-refresh-content-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder Tree</label>
                                            </th>
                                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                                <div class="wpvivid-custom-uploads-tree">
                                                    <div class="wpvivid-custom-tree wpvivid-custom-content-tree-info"></div>
                                                </div>
                                            </td>
                                            <td class="wpvivid-custom-uploads-right">
                                                <div class="wpvivid-custom-uploads-table wpvivid-custom-exclude-content-list">
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="2">
                                                <div style="float: left; margin-right: 10px;">
                                                    <input class="button-primary wpvivid-exclude-content-folder-btn" type="submit" value="Exclude Folders" disabled />
                                                </div>
                                                <small>
                                                    <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                                        <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                                    </div>
                                                </small>
                                                <div style="clear: both;"></div>
                                            </td>
                                        </tr>
                                        </tfoot>
                                        <div style="clear:both;"></div>
                                    </table>
                                    <div style="margin-top: 10px;">
                                        <div style="float: left; margin-right: 10px;">
                                            <input type="text" class="regular-text wpvivid-content-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,\'\')"/>
                                            <input type="button" class="wpvivid-content-extension-rule-btn" value="Save" />
                                        </div>
                                        <small>
                                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                                            </div>
                                        </small>
                                        <div style="clear: both;"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr style="cursor:pointer">
                                <th class="check-column" scope="row" style="padding-left: 6px;">
                                    <label class="screen-reader-text" for=""></label>
                                    <input type="checkbox" name="copy_mu_site_main_additional_file" class="wpvivid-custom-check wpvivid-custom-additional-file-check" />
                                </th>
                                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-additional-file-detail">Additional Files/Folder</td>
                                <td class="column-description desc wpvivid-handle-additional-file-detail additional-file-desc">'.$additional_file_descript.'</td>
                                <th class="wpvivid-handle-additional-file-detail">
                                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                                    </details>
                                </th>
                            </tr>
                            <tr class="wpvivid-custom-detail wpvivid-additional-file-detail wpvivid-close" style="pointer-events: none; opacity: 0.4; display: none;">
                                <th class="check-column"></th>
                                <td colspan="3" class="plugin-title column-primary">
                                    <table class="wp-list-table widefat plugins" style="width:100%;">
                                        <thead>
                                        <tr>
                                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                                <label class="wpvivid-refresh-tree wpvivid-refresh-additional-file-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder/File Tree</label>
                                            </th>
                                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                                <div class="wpvivid-custom-uploads-tree">
                                                    <div class="wpvivid-custom-tree wpvivid-custom-additional-file-tree-info"></div>
                                                </div>
                                            </td>
                                            <td class="wpvivid-custom-uploads-right">
                                                <div class="wpvivid-custom-uploads-table wpvivid-custom-include-additional-file-list">
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="2">
                                                <div style="float: left; margin-right: 10px;">
                                                    <input class="button-primary wpvivid-include-additional-file-btn" type="submit" value="Include folders/files" disabled />
                                                </div>
                                                <small>
                                                    <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                                        <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                                    </div>
                                                </small>
                                                <div style="clear: both;"></div>
                                            </td>
                                        </tr>
                                        </tfoot>
                                        <div style="clear:both;"></div>
                                    </table>
                                    <div style="margin-top: 10px;">
                                        <div style="float: left; margin-right: 10px;">
                                            <input type="text" class="regular-text wpvivid-additional-file-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,\'\')"/>
                                            <input type="button" class="wpvivid-additional-file-extension-rule-btn" value="Save" />
                                        </div>
                                        <small>
                                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                                            </div>
                                        </small>
                                        <div style="clear: both;"></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div style="clear: both;"></div>
                    </div>';

                    if($task->get_site_mu_single())
                    {

                    }
                    else
                    {
                        $html.= ' <p>'.$select_subsite.'</p>';
                        $html.='<p>
                                            <label>
                                                <input type="checkbox" option="wpvividstg_copy_mu_sites" name="mu_all_site" checked />
                                                Select all subsites with their database tables and folders
                                            </label>
                                            <span style="float: right;margin-bottom: 6px">
                                                <label class="screen-reader-text" for="site-search-input">Search A Subsite:</label>
                                                <input type="search" id="wpvivid-mu-site-copy-search-input" name="s" value="">
                                                <input type="submit" id="wpvivid-mu-copy-search-submit" class="button" value="Search A Subsite">
                                            </span>
                                        </p>';
                        $html.='<div id="wpvivid_mu_copy_staging_site_list" style="pointer-events: none; opacity: 0.4;">';
                        $mu_site_list=new WPvivid_Staging_MU_Site_List();
                        if(isset($_POST['page']))
                        {
                            $mu_site_list->set_list($list,'copy_mu_site',$_POST['page']);
                        }
                        else
                        {
                            $mu_site_list->set_list($list,'copy_mu_site');
                        }

                        $mu_site_list->prepare_items();
                        ob_start();
                        $mu_site_list->display();
                        $html .= ob_get_clean();
                    }

                    if($task->get_site_mu_single())
                    {

                    }
                    else
                    {
                        $html.='</div>';
                    }

                    $html .= '</div><div style="clear: both;">';
                    $html.='</div>';

                }

                $wpdb=$old_db;

                $ret['result'] = 'success';
                $ret['html']=$html;
            } else {
                $ret['result'] = 'failed';
                $ret['error'] = 'not found site';
            }
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function push_start_staging()
    {
        $this->ajax_check_security();

        $this->end_shutdown_function=false;
        $task=false;
        try
        {
            if(isset($_POST['id']))
            {
                $task_id=get_option('wpvivid_current_running_staging_task','');

                if($_POST['id']===$task_id)
                {
                    $this->end_shutdown_function=true;
                    die();
                }
            }

            $task_id=get_option('wpvivid_current_running_staging_task','');
            if(!empty($task_id))
            {
                $task=new WPvivid_Staging_Task_Ex($task_id);
                if($task->get_status()==='running')
                {
                    $this->end_shutdown_function=true;
                    die();
                }
                $this->log->OpenLogFile($task->get_log_file_name());
            }
            else
            {
                if(isset($_POST['id']) && isset($_POST['custom_dir']))
                {
                    $option['options'] = $this->set_staging_option();
                    $site_id = $_POST['id'];

                    $list = get_option('wpvivid_staging_task_list',array());
                    $themes_path  = get_theme_root();
                    $plugins_path = WP_PLUGIN_DIR;
                    $upload_dir   = wp_upload_dir();
                    $uploads_path = $upload_dir['basedir'];
                    $content_path = WP_CONTENT_DIR;
                    if(!empty($list))
                    {
                        foreach ($list as $key => $value)
                        {
                            if($key === $site_id)
                            {
                                $site_path = $value['site']['path'];
                                $themes_path  = $site_path . '/wp-content/themes';
                                $plugins_path = $site_path . '/wp-content/plugins';
                                $uploads_path = $site_path . '/wp-content/uploads';
                                $content_path = $site_path . '/wp-content';
                            }
                        }
                    }
                    $is_mu=$_POST['push_mu_site'];

                    $task = new WPvivid_Staging_Task_Ex($site_id);
                    $option['data']['restore'] = true;
                    $option['data']['copy']=false;
                    $json = $_POST['custom_dir'];
                    $json = stripslashes($json);
                    $staging_options = json_decode($json, true);

                    if(is_multisite())
                    {
                        $option['data']['mu']['path_current_site']=$task->get_mu_path_current_site();
                        $option['data']['mu']['main_site_id']=$task->get_mu_main_site_id();
                        $subsites = $task->get_mu_sites();
                        foreach ($subsites as $subsite)
                        {
                            $subsite_id = get_object_vars($subsite)["blog_id"];

                            $str=get_object_vars($subsite)["path"];
                            $option['data']['mu']['site'][$subsite_id]['path_site']=PATH_CURRENT_SITE.substr($str, strlen($option['data']['mu']['path_current_site']));

                            //$option['data']['mu']['site'][$subsite_id]['path_site'] = str_replace($option['data']['mu']['path_current_site'],PATH_CURRENT_SITE,get_object_vars($subsite)["path"]);
                        }
                    }

                    if($is_mu!='false')
                    {
                        $this->set_push_mu_site_option($option,$staging_options,$task,$themes_path,$plugins_path,$uploads_path,$content_path);
                    }
                    else
                    {
                        if($task->get_site_mu_single())
                        {
                            $this->set_push_mu_single_site_option($option,$staging_options,$task,$themes_path,$plugins_path,$uploads_path,$content_path);
                        }
                        else
                        {
                            $task->set_push_staging_history($staging_options);
                            $this->set_push_staging_option($option,$staging_options,$task,$themes_path,$plugins_path,$uploads_path,$content_path);
                        }
                    }


                    $task->set_memory_limit();
                    $task->setup_task($option);
                    $this->log->CreateLogFile($task->get_log_file_name(), 'no_folder', 'staging');
                    $this->log->WriteLog('Start copying the staging site to Live site.', 'notice');
                    $this->log->WriteLogHander();
                }
            }

            $task_id=$task->get_id();
            update_option('wpvivid_current_running_staging_task',$task_id);
            register_shutdown_function(array($this,'deal_shutdown_error'),$task_id);

            $doing=$task->get_doing_task();
            if($doing===false)
            {
                $doing=$task->get_start_next_task();
            }

            $task->set_time_limit();
            if(!$task->do_task($doing))
            {
                $task->finished_task_with_error();

                $this->end_shutdown_function=true;
                die();
            }

            $doing=$task->get_start_next_task();
            if($doing==false)
            {
                $this->log->WriteLog('Copying the staging site is completed.','notice');
                $task->finished_task();
            }
        }
        catch (Exception $error)
        {
            $message = 'An Error has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            error_log($message);
            if($task!==false)
                $task->finished_task_with_error($message);
            $this->log->WriteLog($message,'error');
        }
        $this->end_shutdown_function=true;

        die();
    }

    public function set_push_mu_site_option(&$option,$staging_options,$task,$themes_path,$plugins_path,$uploads_path,$content_path)
    {
        $mu_site_list_json=$staging_options['mu_site_list'];
        $mu_main_site=$staging_options['mu_main_site'];
        $all_site=$staging_options['all_site'];
        $mu_site_list=array();
        foreach ($mu_site_list_json as $site)
        {
            $mu_site_list[$site['id']]['tables']=$site['tables'];
            $mu_site_list[$site['id']]['folders']=$site['folders'];
        }

        $staging_wpdb=$task->get_site_db_instance();

        $subsites = $task->get_mu_sites();
        $mu_exclude_table=array();
        $mu_upload_exclude=array();
        $temp_prefix=$staging_wpdb->base_prefix;
        $staging_wpdb->base_prefix=$task->get_site_prefix();

        if($all_site)
        {

        }
        else
        {
            foreach ($subsites as $subsite)
            {
                $subsite_id = get_object_vars($subsite)["blog_id"];
                if($option['data']['mu']['main_site_id']==$subsite_id)
                    continue;
                if(array_key_exists($subsite_id,$mu_site_list))
                {
                    if($mu_site_list[$subsite_id]['tables']==0)
                    {
                        $prefix=$staging_wpdb->get_blog_prefix($subsite_id);
                        $this->get_table_list($prefix,$mu_exclude_table,$task);
                    }

                    if($mu_site_list[$subsite_id]['folders']==0)
                    {
                        $mu_upload_exclude[]=$this->get_upload_exclude_folder($subsite_id, true, $task);
                    }
                }
                else
                {
                    $prefix=$staging_wpdb->get_blog_prefix($subsite_id);
                    $this->get_table_list($prefix,$mu_exclude_table,$task);
                    $mu_upload_exclude[]=$this->get_upload_exclude_folder($subsite_id,true,$task);
                }
            }
        }

        if($mu_main_site['check'])
        {
            if(!$mu_main_site['tables'])
            {
                $prefix=$staging_wpdb->get_blog_prefix($mu_main_site['id']);
                $this->get_table_list($prefix,$mu_exclude_table,$task);
            }

            if($mu_main_site['upload'])
            {

            }
            else
            {
                $option['data']['upload']['include_regex'][] = '#^' . preg_quote($this->transfer_path($uploads_path . DIRECTORY_SEPARATOR . 'sites'), '/') . '#';
            }

            if($mu_main_site['themes_plugins'])
            {
                $option['data']['theme']['exclude_regex'] = array();
                $option['data']['plugins']['exclude_regex'] = array();
                if ($mu_main_site['themes_check'] == '1')
                {
                    $option['data']['theme']['exclude_regex'] = array();
                    foreach ($mu_main_site['themes_list'] as $theme)
                    {
                        $option['data']['theme']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($themes_path . DIRECTORY_SEPARATOR . $theme), '/') . '#';
                    }
                }

                if ($mu_main_site['plugins_check'] == '1')
                {
                    $option['data']['plugins']['exclude_regex'] = array();
                    foreach ($mu_main_site['plugins_list'] as $plugin)
                    {
                        $option['data']['plugins']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($plugins_path . DIRECTORY_SEPARATOR . $plugin), '/') . '#';
                    }
                }
            }

            if($mu_main_site['wp_content'])
            {
                $option['data']['wp-content']['exclude_regex'] = array();
                $option['data']['wp-content']['exclude_files_regex'] = array();
                foreach ($mu_main_site['content_list'] as $key => $value)
                {
                    $option['data']['wp-content']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($content_path . DIRECTORY_SEPARATOR . $key), '/') . '#';
                }
                if (isset($mu_main_site['content_extension']) && !empty($mu_main_site['content_extension'])) {
                    $str_tmp = explode(',', $mu_main_site['content_extension']);
                    for($index=0; $index<count($str_tmp); $index++){
                        if(!empty($str_tmp[$index])) {
                            $option['data']['wp-content']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                        }
                    }
                }
            }

            if($mu_main_site['additional_file'])
            {
                $custom['exclude_regex'] = array();
                $custom['exclude_files_regex'] = array();
                if (isset($mu_main_site['additional_file_extension']) && !empty($mu_main_site['additional_file_extension']))
                {
                    $str_tmp = explode(',', $mu_main_site['additional_file_extension']);
                    for($index=0; $index<count($str_tmp); $index++)
                    {
                        if(!empty($str_tmp[$index]))
                        {
                            $custom['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                        }
                    }
                }
                foreach ($mu_main_site['additional_file_list'] as $key => $value)
                {
                    $custom['root'] = $key;
                    $option['data']['custom'][] = $custom;
                }
            }

            if($mu_main_site['core'])
            {
                $option['data']['core'] = true;
            }
        }
        else
        {
            $option['data']['upload']['include_regex'][] = '#^' . preg_quote($this->transfer_path($uploads_path . DIRECTORY_SEPARATOR . 'sites'), '/') . '#';
            $prefix=$staging_wpdb->get_blog_prefix($mu_main_site['id']);
            $this->get_table_list($prefix,$mu_exclude_table,$task);
        }

        $staging_wpdb->base_prefix=$temp_prefix;
        $site_prefix=$task->get_site_prefix();
        $option['data']['db']['exclude_tables'] = array();
        $option['data']['db']['exclude_tables'][] = $site_prefix.'hw_blocks';

        foreach ($mu_exclude_table as $table)
        {
            $option['data']['db']['exclude_tables'][] = $table;
        }

        $option['data']['upload']['exclude_regex'] = array();
        foreach ($mu_upload_exclude as $value)
        {
            $option['data']['upload']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path($value), '/').'#';
        }
        $option['data']['upload']['exclude_files_regex'] = array();
        foreach ($mu_main_site['uploads_list'] as $key => $value)
        {
            $option['data']['upload']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($uploads_path . DIRECTORY_SEPARATOR . $key), '/') . '#';
        }
        if (isset($mu_main_site['upload_extension']) && !empty($mu_main_site['upload_extension'])) {
            $str_tmp = explode(',', $mu_main_site['upload_extension']);
            for($index=0; $index<count($str_tmp); $index++){
                if(!empty($str_tmp[$index])) {
                    $option['data']['upload']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                }
            }
        }
    }

    public function set_push_mu_single_site_option(&$option,$staging_options,$task,$themes_path,$plugins_path,$uploads_path,$content_path)
    {
        if($staging_options['themes_check'] == '1')
        {
            $option['data']['theme']['exclude_regex'] = array();
            foreach ($staging_options['themes_list'] as $theme)
            {
                $option['data']['theme']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($themes_path . DIRECTORY_SEPARATOR . $theme), '/') . '#';
            }
        }

        if ($staging_options['plugins_check'] == '1')
        {
            $option['data']['plugins']['exclude_regex'] = array();
            foreach ($staging_options['plugins_list'] as $plugin)
            {
                $option['data']['plugins']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($plugins_path . DIRECTORY_SEPARATOR . $plugin), '/') . '#';
            }
        }

        if($staging_options['content_check']== '1')
        {
            $option['data']['wp-content']['exclude_regex'] = array();
            $option['data']['wp-content']['exclude_files_regex'] = array();
            foreach ($staging_options['content_list'] as $key => $value)
            {
                $option['data']['wp-content']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($content_path . DIRECTORY_SEPARATOR . $key), '/') . '#';
            }
            if (isset($staging_options['content_extension']) && !empty($staging_options['content_extension']))
            {
                $str_tmp = explode(',', $staging_options['content_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $option['data']['wp-content']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                    }
                }
            }
        }

        if($staging_options['additional_file_check']== '1')
        {
            $custom['exclude_regex'] = array();
            $custom['exclude_files_regex'] = array();
            if (isset($staging_options['additional_file_extension']) && !empty($staging_options['additional_file_extension']))
            {
                $str_tmp = explode(',', $staging_options['additional_file_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $custom['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                    }
                }
            }
            foreach ($staging_options['additional_file_list'] as $key => $value)
            {
                $custom['root'] = $key;
                $option['data']['custom'][] = $custom;
            }
        }

        if($staging_options['core_check']== '1')
        {
            $option['data']['core'] = true;
        }

        if ($staging_options['database_check'] == '1')
        {
            $site_prefix=$task->get_site_prefix();
            $option['data']['db']['exclude_tables'] = array();
            $option['data']['db']['exclude_tables'][] = $site_prefix.'hw_blocks';
            $option['data']['db']['exclude_tables'][] = $site_prefix.'users';
            $option['data']['db']['exclude_tables'][] = $site_prefix.'usermate';
            $option['data']['db']['exclude_tables'][] =$site_prefix.'site';
            $option['data']['db']['exclude_tables'][] =$site_prefix.'sitemeta';
            $option['data']['db']['exclude_tables'][] =$site_prefix.'blogs';
            $option['data']['db']['exclude_tables'][] =$site_prefix.'blogmeta';

            foreach ($staging_options['database_list'] as $table)
            {
                $option['data']['db']['exclude_tables'][] = $table;
            }
        }

        if ($staging_options['uploads_check'] == '1')
        {
            $option['data']['upload']['exclude_regex'] = array();
            $option['data']['upload']['exclude_files_regex'] = array();
            foreach ($staging_options['uploads_list'] as $key => $value)
            {
                $option['data']['upload']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($uploads_path . DIRECTORY_SEPARATOR . $key), '/') . '#';
            }
            if (isset($staging_options['upload_extension']) && !empty($staging_options['upload_extension']))
            {
                $str_tmp = explode(',', $staging_options['upload_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $option['data']['upload']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                    }
                }
            }
        }
    }

    public function set_push_staging_option(&$option,$staging_options,$task,$themes_path,$plugins_path,$uploads_path,$content_path)
    {
        if ($staging_options['database_check'] == '1')
        {
            $site_prefix=$task->get_site_prefix();
            $option['data']['db']['exclude_tables'] = array();
            $option['data']['db']['exclude_tables'][] = $site_prefix.'hw_blocks';
            //$option['data']['db']['exclude_tables'][] = $site_prefix.'blogs';
            foreach ($staging_options['database_list'] as $table)
            {
                $option['data']['db']['exclude_tables'][] = $table;
            }
        }

        if ($staging_options['themes_check'] == '1')
        {
            $option['data']['theme']['exclude_regex'] = array();
            foreach ($staging_options['themes_list'] as $theme)
            {
                $option['data']['theme']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($themes_path . DIRECTORY_SEPARATOR . $theme), '/') . '#';
            }
        }

        if ($staging_options['plugins_check'] == '1')
        {
            $option['data']['plugins']['exclude_regex'] = array();
            foreach ($staging_options['plugins_list'] as $plugin)
            {
                $option['data']['plugins']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($plugins_path . DIRECTORY_SEPARATOR . $plugin), '/') . '#';
            }
        }

        if ($staging_options['uploads_check'] == '1')
        {
            $option['data']['upload']['exclude_regex'] = array();
            foreach ($staging_options['uploads_list'] as $key => $value)
            {
                $option['data']['upload']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($uploads_path . DIRECTORY_SEPARATOR . $key), '/') . '#';
            }
            $option['data']['upload']['exclude_files_regex'] = array();
            if (isset($staging_options['upload_extension']) && !empty($staging_options['upload_extension'])) {
                $str_tmp = explode(',', $staging_options['upload_extension']);
                for($index=0; $index<count($str_tmp); $index++){
                    if(!empty($str_tmp[$index])) {
                        $option['data']['upload']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                    }
                }
            }
        }

        if ($staging_options['content_check'] == '1')
        {
            $option['data']['wp-content']['exclude_regex'] = array();
            foreach ($staging_options['content_list'] as $key => $value)
            {
                $option['data']['wp-content']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($content_path . DIRECTORY_SEPARATOR . $key), '/') . '#';
            }
            $option['data']['wp-content']['exclude_files_regex'] = array();
            if (isset($staging_options['content_extension']) && !empty($staging_options['content_extension'])) {
                $str_tmp = explode(',', $staging_options['content_extension']);
                for($index=0; $index<count($str_tmp); $index++){
                    if(!empty($str_tmp[$index])) {
                        $option['data']['wp-content']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                    }
                }
            }
        }

        if ($staging_options['core_check'] == '1')
        {
            $option['data']['core'] = true;
        }

        if ($staging_options['additional_file_check'] == '1')
        {
            $custom['exclude_regex'] = array();
            $custom['exclude_files_regex'] = array();
            if (isset($staging_options['additional_file_extension']) && !empty($staging_options['additional_file_extension']))
            {
                $str_tmp = explode(',', $staging_options['additional_file_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $custom['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                    }
                }
            }
            foreach ($staging_options['additional_file_list'] as $key => $value)
            {
                $custom['root'] = $key;
                $option['data']['custom'][] = $custom;
            }
        }
    }

    public function push_restart_staging()
    {
        $task=false;
        $this->end_shutdown_function=false;
        try
        {
            $task_id=get_option('wpvivid_current_running_staging_task','');
            if(!empty($task_id))
            {
                $task=new WPvivid_Staging_Task_Ex($task_id);
                if($task->get_status()==='running')
                {
                    $this->end_shutdown_function=true;
                    die();
                }
                $this->log->OpenLogFile($task->get_log_file_name());
            }
            else
            {
                $this->end_shutdown_function=true;
                die();
            }

            $task_id=$task->get_id();
            update_option('wpvivid_current_running_staging_task',$task_id);

            $doing=$task->get_doing_task();
            if($doing===false)
            {
                $doing=$task->get_start_next_task();
            }
            register_shutdown_function(array($this,'deal_shutdown_error'),$task_id);
            $task->set_time_limit();
            if(!$task->do_task($doing))
            {
                $task->finished_task_with_error();
                $this->end_shutdown_function=true;
                die();
            }

            $doing=$task->get_start_next_task();
            if($doing==false)
            {
                $this->log->WriteLog('Copying the staging site is completed.','notice');
                $task->finished_task();
            }
        }
        catch (Exception $error)
        {
            $message = 'An Error has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            error_log($message);
            if($task!==false)
                $task->finished_task_with_error($message);
            $this->log->WriteLog($message,'error');
        }
        $this->end_shutdown_function=true;
        die();
    }

    public function copy_start_staging()
    {
        $this->ajax_check_security();

        $this->end_shutdown_function=false;
        $task=false;
        try
        {
            if(isset($_POST['id']))
            {
                $task_id=get_option('wpvivid_current_running_staging_task','');

                if($_POST['id']===$task_id)
                {
                    $this->end_shutdown_function=true;
                    die();
                }
            }

            $task_id=get_option('wpvivid_current_running_staging_task','');
            if(!empty($task_id))
            {
                $task=new WPvivid_Staging_Task_Ex($task_id);
                if($task->get_status()==='running')
                {
                    $this->end_shutdown_function=true;
                    die();
                }
                $this->log->OpenLogFile($task->get_log_file_name());
            }
            else
            {
                if(isset($_POST['id']) && isset($_POST['custom_dir']))
                {
                    $option['options'] = $this->set_staging_option();
                    $site_id = $_POST['id'];

                    $list = get_option('wpvivid_staging_task_list',array());
                    $themes_path  = get_theme_root();
                    $plugins_path = WP_PLUGIN_DIR;
                    $upload_dir   = wp_upload_dir();
                    $uploads_path = $upload_dir['basedir'];
                    $content_path = WP_CONTENT_DIR;
                    /*if(!empty($list))
                    {
                        foreach ($list as $key => $value)
                        {
                            if($key === $site_id)
                            {
                                $site_path = $value['site']['path'];
                                $themes_path  = $site_path . '/wp-content/themes';
                                $plugins_path = $site_path . '/wp-content/plugins';
                                $uploads_path = $site_path . '/wp-content/uploads';
                                $content_path = $site_path . '/wp-content';
                            }
                        }
                    }*/
                    $is_mu=$_POST['push_mu_site'];

                    $task = new WPvivid_Staging_Task_Ex($site_id);

                    $json = $_POST['custom_dir'];
                    $json = stripslashes($json);
                    $staging_options = json_decode($json, true);

                    global $wpdb;

                    $option['data']['restore'] = false;
                    $option['data']['copy']=true;
                    if(is_multisite())
                    {
                        $option['data']['mu']['path_current_site']=$task->get_mu_path_current_site();
                        $subsites = get_sites();
                        foreach ($subsites as $subsite)
                        {
                            $subsite_id = get_object_vars($subsite)["blog_id"];

                            $str=get_object_vars($subsite)["path"];
                            $option['data']['mu']['site'][$subsite_id]['path_site']=$option['data']['mu']['path_current_site'].substr($str, strlen(PATH_CURRENT_SITE));

                            //$option['data']['mu']['site'][$subsite_id]['path_site'] = str_replace(PATH_CURRENT_SITE,$option['data']['mu']['path_current_site'],get_object_vars($subsite)["path"]);
                            if(is_main_site($subsite_id))
                            {
                                $option['data']['mu']['main_site_id']=$subsite_id;
                            }
                        }
                    }

                    if($is_mu!='false')
                    {
                        $this->set_copy_mu_site_option($option,$staging_options,$themes_path,$plugins_path,$uploads_path,$content_path);
                    }
                    else
                    {
                        if($task->get_site_mu_single())
                        {
                            $this->set_copy_mu_single_site_option($option,$staging_options,$task,$themes_path,$plugins_path,$uploads_path,$content_path);
                        }
                        else
                        {
                            $this->set_copy_staging_option($option,$staging_options,$themes_path,$plugins_path,$uploads_path,$content_path);
                        }
                    }

                    $task->set_memory_limit();
                    $task->setup_task($option);
                    $this->log->CreateLogFile($task->get_log_file_name(), 'no_folder', 'staging');
                    $this->log->WriteLog('Start copying the Live site to staging site.', 'notice');
                    $this->log->WriteLogHander();
                }
            }

            $task_id=$task->get_id();
            update_option('wpvivid_current_running_staging_task',$task_id);
            register_shutdown_function(array($this,'deal_shutdown_error'),$task_id);

            $doing=$task->get_doing_task();
            if($doing===false)
            {
                $doing=$task->get_start_next_task();
            }

            $task->set_time_limit();
            if(!$task->do_task($doing))
            {
                $task->finished_task_with_error();

                $this->end_shutdown_function=true;
                die();
            }

            $doing=$task->get_start_next_task();
            if($doing==false)
            {
                $this->log->WriteLog('Copying the staging site is completed.','notice');
                $task->finished_task();
            }
        }
        catch (Exception $error)
        {
            $message = 'An Error has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            error_log($message);
            if($task!==false)
                $task->finished_task_with_error($message);
            $this->log->WriteLog($message,'error');
        }
        $this->end_shutdown_function=true;

        die();
    }

    public function set_copy_mu_site_option(&$option,$staging_options,$themes_path,$plugins_path,$uploads_path,$content_path)
    {
        global $wpdb;
        $mu_site_list_json=$staging_options['mu_site_list'];
        $mu_main_site=$staging_options['mu_main_site'];
        $all_site=$staging_options['all_site'];
        $mu_site_list=array();
        foreach ($mu_site_list_json as $site)
        {
            $mu_site_list[$site['id']]['tables']=$site['tables'];
            $mu_site_list[$site['id']]['folders']=$site['folders'];
        }

        $subsites = get_sites();
        $mu_exclude_table=array();
        $mu_upload_exclude=array();

        if($all_site)
        {

        }
        else
        {
            foreach ($subsites as $subsite)
            {
                $subsite_id = get_object_vars($subsite)["blog_id"];
                if(is_main_site($subsite_id))
                    continue;
                if(array_key_exists($subsite_id,$mu_site_list))
                {
                    if($mu_site_list[$subsite_id]['tables']==0)
                    {
                        $prefix=$wpdb->get_blog_prefix($subsite_id);
                        $this->get_table_list($prefix,$mu_exclude_table);
                    }

                    if($mu_site_list[$subsite_id]['folders']==0)
                    {
                        $mu_upload_exclude[]=$this->get_upload_exclude_folder($subsite_id);
                    }
                }
                else
                {
                    $prefix=$wpdb->get_blog_prefix($subsite_id);
                    $this->get_table_list($prefix,$mu_exclude_table);
                    $mu_upload_exclude[]=$this->get_upload_exclude_folder($subsite_id);
                }
            }
        }

        if($mu_main_site['check'])
        {
            if(!$mu_main_site['tables'])
            {
                $prefix=$wpdb->get_blog_prefix($mu_main_site['id']);
                $this->get_table_list($prefix,$mu_exclude_table);
            }

            if($mu_main_site['upload'])
            {

            }
            else
            {
                $option['data']['upload']['include_regex'][] = '#^' . preg_quote($this->transfer_path($uploads_path . DIRECTORY_SEPARATOR . 'sites'), '/') . '#';
            }

            if($mu_main_site['themes_plugins'])
            {
                $option['data']['theme']['exclude_regex'] = array();
                $option['data']['plugins']['exclude_regex'] = array();
                if ($mu_main_site['themes_check'] == '1')
                {
                    $option['data']['theme']['exclude_regex'] = array();
                    foreach ($mu_main_site['themes_list'] as $theme)
                    {
                        $option['data']['theme']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($themes_path . DIRECTORY_SEPARATOR . $theme), '/') . '#';
                    }
                }

                if ($mu_main_site['plugins_check'] == '1')
                {
                    $option['data']['plugins']['exclude_regex'] = array();
                    foreach ($mu_main_site['plugins_list'] as $plugin)
                    {
                        $option['data']['plugins']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($plugins_path . DIRECTORY_SEPARATOR . $plugin), '/') . '#';
                    }
                }
            }

            if($mu_main_site['wp_content'])
            {
                $option['data']['wp-content']['exclude_regex'] = array();
                $option['data']['wp-content']['exclude_files_regex'] = array();
                foreach ($mu_main_site['content_list'] as $key => $value)
                {
                    $option['data']['wp-content']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($content_path . DIRECTORY_SEPARATOR . $key), '/') . '#';
                }
                if (isset($mu_main_site['content_extension']) && !empty($mu_main_site['content_extension'])) {
                    $str_tmp = explode(',', $mu_main_site['content_extension']);
                    for($index=0; $index<count($str_tmp); $index++){
                        if(!empty($str_tmp[$index])) {
                            $option['data']['wp-content']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                        }
                    }
                }
            }

            if($mu_main_site['additional_file'])
            {
                $custom['exclude_regex'] = array();
                $custom['exclude_files_regex'] = array();
                if (isset($mu_main_site['additional_file_extension']) && !empty($mu_main_site['additional_file_extension']))
                {
                    $str_tmp = explode(',', $mu_main_site['additional_file_extension']);
                    for($index=0; $index<count($str_tmp); $index++)
                    {
                        if(!empty($str_tmp[$index]))
                        {
                            $custom['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                        }
                    }
                }
                foreach ($mu_main_site['additional_file_list'] as $key => $value)
                {
                    $custom['root'] = $key;
                    $option['data']['custom'][] = $custom;
                }
            }

            if($mu_main_site['core'])
            {
                $option['data']['core'] = true;
            }
        }
        else
        {
            $prefix=$wpdb->get_blog_prefix($mu_main_site['id']);
            $this->get_table_list($prefix,$mu_exclude_table);
            $option['data']['upload']['include_regex'][] = '#^' . preg_quote($this->transfer_path($uploads_path . DIRECTORY_SEPARATOR . 'sites'), '/') . '#';
        }

        $site_prefix=$wpdb->base_prefix;
        $option['data']['db']['exclude_tables'] = array();
        $option['data']['db']['exclude_tables'][] = $site_prefix.'hw_blocks';

        foreach ($mu_exclude_table as $table)
        {
            $option['data']['db']['exclude_tables'][] = $table;
        }

        $option['data']['upload']['exclude_regex'] = array();
        foreach ($mu_upload_exclude as $value)
        {
            $option['data']['upload']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path($value), '/').'#';
        }
        $option['data']['upload']['exclude_files_regex'] = array();
        foreach ($mu_main_site['uploads_list'] as $key => $value)
        {
            $option['data']['upload']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($uploads_path . DIRECTORY_SEPARATOR . $key), '/') . '#';
        }
        if (isset($mu_main_site['upload_extension']) && !empty($mu_main_site['upload_extension'])) {
            $str_tmp = explode(',', $mu_main_site['upload_extension']);
            for($index=0; $index<count($str_tmp); $index++){
                if(!empty($str_tmp[$index])) {
                    $option['data']['upload']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                }
            }
        }
    }

    public function set_copy_mu_single_site_option(&$option,$staging_options,$task,$themes_path,$plugins_path,$uploads_path,$content_path)
    {
        global $wpdb;
        $mu_single_site_id=$task->get_site_mu_single_site_id();

        $option['data']['mu_single']=true;
        $upload_path=$this->get_upload_exclude_folder($mu_single_site_id);
        $option['data']['mu_single_upload']=str_replace(ABSPATH,'',$upload_path);
        $option['data']['mu_single_site_id']=$mu_single_site_id;

        $subsites = get_sites();
        $mu_exclude_table=array();
        $mu_upload_exclude=array();
        foreach ($subsites as $subsite)
        {
            $subsite_id = get_object_vars($subsite)["blog_id"];
            if($mu_single_site_id==$subsite_id)
            {
            }
            else
            {
                $prefix=$wpdb->get_blog_prefix($subsite_id);
                $this->get_table_list($prefix,$mu_exclude_table,false,false);
                if(!is_main_site($subsite_id))
                    $mu_upload_exclude[]=$this->get_upload_exclude_folder($subsite_id);
            }
        }

        if ($staging_options['database_check'] == '1')
        {
            $site_prefix=$wpdb->base_prefix;
            $option['data']['db']['exclude_tables'] = array();
            $option['data']['db']['exclude_tables'][] = $site_prefix.'hw_blocks';
            $option['data']['db']['exclude_tables'][] =$site_prefix.'site';
            $option['data']['db']['exclude_tables'][] =$site_prefix.'sitemeta';
            $option['data']['db']['exclude_tables'][] =$site_prefix.'blogs';
            $option['data']['db']['exclude_tables'][] =$site_prefix.'blogmeta';

            foreach ($staging_options['database_list'] as $table)
            {
                $option['data']['db']['exclude_tables'][] = $table;
            }

            foreach ($mu_exclude_table as $table)
            {
                $option['data']['db']['exclude_tables'][] = $table;
            }
        }

        if ($staging_options['themes_check'] == '1')
        {
            $option['data']['theme']['exclude_regex'] = array();
            foreach ($staging_options['themes_list'] as $theme)
            {
                $option['data']['theme']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($themes_path . DIRECTORY_SEPARATOR . $theme), '/') . '#';
            }
        }

        if ($staging_options['plugins_check'] == '1')
        {
            $option['data']['plugins']['exclude_regex'] = array();
            foreach ($staging_options['plugins_list'] as $plugin)
            {
                $option['data']['plugins']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($plugins_path . DIRECTORY_SEPARATOR . $plugin), '/') . '#';
            }
        }

        if ($staging_options['uploads_check'] == '1')
        {
            $option['data']['upload']['exclude_regex'] = array();

            foreach ($mu_upload_exclude as $value)
            {
                $option['data']['upload']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path($value), '/').'#';
            }

            foreach ($staging_options['uploads_list'] as $key => $value)
            {
                $option['data']['upload']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($uploads_path . DIRECTORY_SEPARATOR . $key), '/') . '#';
            }
            $option['data']['upload']['exclude_files_regex'] = array();
            if (isset($staging_options['upload_extension']) && !empty($staging_options['upload_extension']))
            {
                $str_tmp = explode(',', $staging_options['upload_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $option['data']['upload']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                    }
                }
            }
        }

        if ($staging_options['content_check'] == '1')
        {
            $option['data']['wp-content']['exclude_regex'] = array();
            foreach ($staging_options['content_list'] as $key => $value)
            {
                $option['data']['wp-content']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($content_path . DIRECTORY_SEPARATOR . $key), '/') . '#';
            }
            $option['data']['wp-content']['exclude_files_regex'] = array();
            if (isset($staging_options['content_extension']) && !empty($staging_options['content_extension'])) {
                $str_tmp = explode(',', $staging_options['content_extension']);
                for($index=0; $index<count($str_tmp); $index++){
                    if(!empty($str_tmp[$index])) {
                        $option['data']['wp-content']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                    }
                }
            }
        }

        if ($staging_options['core_check'] == '1')
        {
            $option['data']['core'] = true;
        }

        if ($staging_options['additional_file_check'] == '1')
        {
            $custom['exclude_regex'] = array();
            $custom['exclude_files_regex'] = array();
            if (isset($staging_options['additional_file_extension']) && !empty($staging_options['additional_file_extension']))
            {
                $str_tmp = explode(',', $staging_options['additional_file_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $custom['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                    }
                }
            }
            foreach ($staging_options['additional_file_list'] as $key => $value)
            {
                $custom['root'] = $key;
                $option['data']['custom'][] = $custom;
            }
        }
    }

    public function set_copy_staging_option(&$option,$staging_options,$themes_path,$plugins_path,$uploads_path,$content_path)
    {
        global $wpdb;

        if ($staging_options['database_check'] == '1')
        {
            $site_prefix=$wpdb->base_prefix;
            $option['data']['db']['exclude_tables'] = array();
            $option['data']['db']['exclude_tables'][] = $site_prefix.'hw_blocks';
            foreach ($staging_options['database_list'] as $table)
            {
                $option['data']['db']['exclude_tables'][] = $table;
            }
        }

        if ($staging_options['themes_check'] == '1')
        {
            $option['data']['theme']['exclude_regex'] = array();
            foreach ($staging_options['themes_list'] as $theme)
            {
                $option['data']['theme']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($themes_path . DIRECTORY_SEPARATOR . $theme), '/') . '#';
            }
        }

        if ($staging_options['plugins_check'] == '1')
        {
            $option['data']['plugins']['exclude_regex'] = array();
            foreach ($staging_options['plugins_list'] as $plugin)
            {
                $option['data']['plugins']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($plugins_path . DIRECTORY_SEPARATOR . $plugin), '/') . '#';
            }
        }

        if ($staging_options['uploads_check'] == '1')
        {
            $option['data']['upload']['exclude_regex'] = array();
            foreach ($staging_options['uploads_list'] as $key => $value)
            {
                $option['data']['upload']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($uploads_path . DIRECTORY_SEPARATOR . $key), '/') . '#';
            }
            $option['data']['upload']['exclude_files_regex'] = array();
            if (isset($staging_options['upload_extension']) && !empty($staging_options['upload_extension'])) {
                $str_tmp = explode(',', $staging_options['upload_extension']);
                for($index=0; $index<count($str_tmp); $index++){
                    if(!empty($str_tmp[$index])) {
                        $option['data']['upload']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                    }
                }
            }
        }

        if ($staging_options['content_check'] == '1')
        {
            $option['data']['wp-content']['exclude_regex'] = array();
            foreach ($staging_options['content_list'] as $key => $value)
            {
                $option['data']['wp-content']['exclude_regex'][] = '#^' . preg_quote($this->transfer_path($content_path . DIRECTORY_SEPARATOR . $key), '/') . '#';
            }
            $option['data']['wp-content']['exclude_files_regex'] = array();
            if (isset($staging_options['content_extension']) && !empty($staging_options['content_extension'])) {
                $str_tmp = explode(',', $staging_options['content_extension']);
                for($index=0; $index<count($str_tmp); $index++){
                    if(!empty($str_tmp[$index])) {
                        $option['data']['wp-content']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                    }
                }
            }
        }

        if ($staging_options['core_check'] == '1')
        {
            $option['data']['core'] = true;
        }

        if ($staging_options['additional_file_check'] == '1')
        {
            $custom['exclude_regex'] = array();
            $custom['exclude_files_regex'] = array();
            if (isset($staging_options['additional_file_extension']) && !empty($staging_options['additional_file_extension']))
            {
                $str_tmp = explode(',', $staging_options['additional_file_extension']);
                for($index=0; $index<count($str_tmp); $index++)
                {
                    if(!empty($str_tmp[$index]))
                    {
                        $custom['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                    }
                }
            }
            foreach ($staging_options['additional_file_list'] as $key => $value)
            {
                $custom['root'] = $key;
                $option['data']['custom'][] = $custom;
            }
        }

        self::wpvivid_set_staging_history($staging_options);
    }

    public function copy_restart_staging()
    {
        $task=false;
        $this->end_shutdown_function=false;
        try
        {
            $task_id=get_option('wpvivid_current_running_staging_task','');
            if(!empty($task_id))
            {
                $task=new WPvivid_Staging_Task_Ex($task_id);
                if($task->get_status()==='running')
                {
                    $this->end_shutdown_function=true;
                    die();
                }
                $this->log->OpenLogFile($task->get_log_file_name());
            }
            else
            {
                $this->end_shutdown_function=true;
                die();
            }

            $task_id=$task->get_id();
            update_option('wpvivid_current_running_staging_task',$task_id);

            $doing=$task->get_doing_task();
            if($doing===false)
            {
                $doing=$task->get_start_next_task();
            }
            register_shutdown_function(array($this,'deal_shutdown_error'),$task_id);
            $task->set_time_limit();
            if(!$task->do_task($doing))
            {
                $task->finished_task_with_error();
                $this->end_shutdown_function=true;
                die();
            }

            $doing=$task->get_start_next_task();
            if($doing==false)
            {
                $this->log->WriteLog('Copying the staging site is completed.','notice');
                $task->finished_task();
            }
        }
        catch (Exception $error)
        {
            $message = 'An Error has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            error_log($message);
            if($task!==false)
                $task->finished_task_with_error($message);
            $this->log->WriteLog($message,'error');
        }
        $this->end_shutdown_function=true;
        die();
    }

    public function create_new_prefix($use_additional_db)
    {
        if($use_additional_db)
        {
            global $wpdb;
            $prefix=$wpdb->base_prefix;
        }
        else
        {
            global $wpdb;
            $prefix='';
            $site_id=0;
            while(1)
            {
                $prefix='wpvividstg'.$site_id.'_';
                $sql=$wpdb->prepare("SHOW TABLES LIKE %s;", $wpdb->esc_like($prefix) . '%');
                $result = $wpdb->get_results($sql, OBJECT_K);
                if(empty($result))
                {
                    break;
                }
                $site_id++;
            }
        }

        return $prefix;
    }

    public function export_setting_addon($json)
    {
        $default = array();
        $wpvivid_staging_history = get_option('wpvivid_staging_history', $default);
        $wpvivid_push_staging_history = get_option('wpvivid_push_staging_history', $default);
        $json['data']['wpvivid_staging_history'] = $wpvivid_staging_history;
        $json['data']['wpvivid_push_staging_history'] = $wpvivid_push_staging_history;
        return $json;
    }
}







