<?php get_header(); ?>

<h2> Welcome </h2>
<pre>
<?php if ( isset( $_GET['form_submited'] ) && $_GET['form_submited'] == '1' ) {

    get_template_part('create', 'trial');

} else { ?>
</pre>

<div id="conainer">
    <form class="register_form" action="/" method="GET">
        <fieldset>
            <input type="hidden" name="form_submited" value="1"/>
            <label for="username">Username</label>
            <input type="text" name="username" />
        </fieldset>

        <fieldset>
            <label for="user_email">Email</label>
            <input type="email" name="user_email" />
        </fieldset>
        <fieldset>
            <label for="theme">Select a theme which you want to test</label>
            <select name="theme" id="theme" required="required">
                <option value="">Select a theme</option>
                <option value="2_senna">Senna</option>
                <option value="3_swipe">Swipe</option>
            </select>
        </fieldset>
        <input type="submit" value="Register"/>
    </form>
</div>

<?php }

get_footer();