<?php
/*
Template Name: Home Page
*/
get_header();  ?>

    <div class="span9">
        <div class="hero-unit row">
            <div id="overlay-loader"></div>
            <?php
            global $trial;
            if ( isset( $_GET['form_submited'] ) && $_GET['form_submited'] == '1' ) {
                echo '<div class="submited">';
                get_template_part('create', 'trial');
                echo '</div>';
            } else { ?>
                <div class="span4">
                    <h2>One click trial install!</h2>
                    <p>Ok I've lied ... you will need to confirm it with a second click via email -_- </p>
                </div>
                <div class="span5 register_form">
                    <form id="register_form" action="/" method="GET">

                        <fieldset class="advanced hide">
                            <input type="hidden" name="form_submited" value="1"/>
<!--                            <label for="username">Username</label>-->
                            <input type="text" name="username" placeholder="Username" disabled="disabled" />
                        </fieldset>

                        <fieldset class="advanced hide">
                            <!--                            <label for="username">Username</label>-->
                            <input type="text" name="path" placeholder="Path" disabled="disabled" />
                        </fieldset>

                        <fieldset class="advanced hide row-fluid">
                            <input class="span1 pull-left" type="checkbox" name="newsletter" placeholder="Newsletter" disabled="disabled" />
                            <label class="span4" for="newsletter">Disable the newsletter</label>
                        </fieldset>

                        <fieldset>
<!--                            <label for="user_email">Email</label>-->
                            <input type="email" name="user_email" placeholder="Email" />
                        </fieldset>

                        <fieldset>
<!--                            <label for="theme">Select a theme which you want to test</label>-->
                            <select name="theme" id="theme" required="required">
                                <option value="">Select a theme</option>
                                <?php $trial->generate_themes_select_options(); ?>
                            </select>
                        </fieldset>
                        <input class="btn btn-primary btn-large" type="submit" value="Create Trial"/>
                        <a id="advanced_settings" class="btn-small" href="#">Advanced settings</a>
                    </form>
                </div>

            <?php }?>

        </div>

        <div class="row success">
            <div class="span4 rule-box">
                <h2>You can test</h2>
                <p>Here you can test our themes by simply registering a trial.</p>
                <p>You will recive an email with credentials for your trial and you are ready to knock yourself up.</p>
                <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
            <div class="span4 rule-box">
                <h2>Decide in 2 weeks</h2>
                <p>Your trial will live 2 weeks but I guess is enough since you will love the theme and buy it </p>
                <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
            <div class="span4 rule-box">
                <h2>Heading</h2>
                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
                <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
        </div><!--/row-->
        <div class="row warning">
            <div class="span4 rule-box">
                <h2>No plugins ?</h2>
                <p>Yeah that's true. </p>
                <p>You cannot install plugins but if you make an request to us we can solve this out</p>
                <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
            <div class="span4 rule-box">
                <h2>My space ?</h2>
                <p>Upload space is limited to 10mb.</p>
                <p>You wont actually need it since you already have a bunch of photos and videos in your demo.</p>
                <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
            <div class="span4 rule-box">
                <h2>Heading</h2>
                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
                <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
        </div><!--/row-->
    </div><!--/span-->
    </div><!--/row-->

<?php get_footer();