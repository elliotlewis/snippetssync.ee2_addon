<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * test
 *
 * @package		Snippetssync
 * @subpackage	ThirdParty
 * @category	Modules
 * @author		Bjørn Børresen
 * @link		http://www.addonbakery.com
 */

class Snippetssync_mcp
{
	var $base;			// the base url for this module
	var $form_base;		// base url for forms
	var $module_name = "snippetssync";

    var $EE;

	public function __construct( $switch = TRUE )
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		$this->base	 	 = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->module_name;
		$this->form_base = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->module_name;

        // load local config if none of these values aren't already specified in a master config
        if(!isset($this->EE->config->config['snippetssync_production_mode'])
            && !isset($this->EE->config->config['snippetssync_snippet_prefix'])
            && !isset($this->EE->config->config['snippetssync_global_variable_prefix'])) {
            $this->EE->load->config('snippetssync');
        }
    }

	public function index()
	{
        // snippetssync_production_mode_override for backwards compatibility
		$vars = array(
            'production_mode' => $this->EE->config->item('snippetssync_production_mode_override') || $this->EE->config->item('snippetssync_production_mode'),
        );
		return $this->content_wrapper('index', 'snippetssync_welcome', $vars);
	}

    public function manual_sync()
    {
        $this->EE->load->library('snippetslib');
        $success = $this->EE->snippetslib->sync_all();
        $global_variables = $this->EE->snippetslib->last_sync_log['globals'];
        $snippets = $this->EE->snippetslib->last_sync_log['snippets'];

        if (version_compare(APP_VER, '2.6', '>=')) {
            $sync_time =  $this->EE->localize->human_time(NULL, TRUE, TRUE);
        } else {
            $sync_time = $this->EE->localize->set_human_time('', TRUE, TRUE);   // #eecms - the cms where get functions are prefixed with set! :-p
        }

        $vars = array(
            'success' => $success,
            'error_message' => $this->EE->snippetslib->error_message,
            'sync_time' => $sync_time,
            'global_variables' => $global_variables,
            'snippets' => $snippets,
            'global_variables_count' => count($global_variables),
            'snippets_count' => count($snippets),
            'ignored_count' => isset($this->EE->snippetslib->last_sync_log['ignored']) ? count($this->EE->snippetslib->last_sync_log['ignored']) : 0,
            'ignored' => isset($this->EE->snippetslib->last_sync_log['ignored']) ? $this->EE->snippetslib->last_sync_log['ignored'] : FALSE,
        );

        return $this->content_wrapper('synced', 'snippetssync_synced', $vars);
    }

	public function content_wrapper($content_view, $lang_key, $vars = array())
	{
		$vars['content_view'] = $content_view;
		$vars['_base'] = $this->base;
		$vars['_form_base'] = $this->form_base;

        // $this->EE->cp->set_variable was deprecated in 2.6
        if (version_compare(APP_VER, '2.6', '>=')) {
            $this->EE->view->cp_page_title = lang($lang_key);
        } else {
            $this->EE->cp->set_variable('cp_page_title', lang($lang_key));
        }

		$this->EE->cp->set_breadcrumb($this->base, lang('snippetssync_module_name'));

		return $this->EE->load->view('_wrapper', $vars, TRUE);
	}
}

/* End of file mcp.snippetssync.php */
/* Location: ./system/expressionengine/third_party/snippetssync/mcp.snippetssync.php */
/* Generated by DevKit for EE - develop addons faster! */