{extends file='page.tpl'}

{block name=main}
    <div class="row">
        <div class="col-xs-12 col-md-6 text-center">
            <p class="home-choice">
                <a href="{$SERVER_URL}create_poll.php?type=date" class="opacity" role="button">
                    <img class="img-responsive center-block" src="{'images/date.png'|resource}" alt=""/>
                    <br/>
                    <span class="btn btn-primary btn-lg">
                        <span class="glyphicon glyphicon-calendar"></span>
                        {__('Homepage', 'Schedule an event')}
                    </span>
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-md-6 text-center">
            <p class="home-choice">
                <a href="{$SERVER_URL}create_poll.php?type=autre" class="opacity" role="button">
                    <img alt="" class="img-responsive center-block" src="{'images/classic.png'|resource}"/>
                    <br/>
                    <span class="btn btn-info btn-lg">
                        <span class="glyphicon glyphicon-stats"></span>
                        {__('Homepage', 'Make a classic poll')}
                    </span>
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-md-6 col-md-offset-3 text-center">
            <p class="home-choice">
                <a href="{$SERVER_URL}find_polls.php" class="opacity" role="button">
                    <span class="btn btn-warning btn-lg">
                        <span class="glyphicon glyphicon-search"></span>
                        {__('Homepage', 'Where are my polls')}
                    </span>
                </a>
            </p>
        </div>
    </div>
    <hr role="presentation"/>
    <div class="row">

        {if $show_what_is_that}
            <div class="col-md-{$col_size}">
                <h3>{__('1st section', 'What is that?')}</h3>

                <p class="text-center" role="presentation">
                    <span class="glyphicon glyphicon-question-sign" style="font-size:50px"></span>
                </p>

                <p>{__('1st section', 'Framadate is an online service for planning an appointment or make a decision quickly and easily. No registration is required.')}</p>

                <p>{__('1st section', 'Here is how it works:')}</p>
                <ol>
                    <li>{__('1st section', 'Make a poll')}</li>
                    <li>{__('1st section', 'Define dates or subjects to choose')}</li>
                    <li>{__('1st section', 'Send the poll link to your friends or colleagues')}</li>
                    <li>{__('1st section', 'Discuss and make a decision')}</li>
                </ol>

                {if $demo_poll != null}
                <p>
                    {__('1st section', 'Do you want to')}
                    <a href="{poll_url id='aqg259dth55iuhwm'}">{__('1st section', 'view an example?')}</a>
                </p>
                {/if}
            </div>
        {/if}
        {if $show_the_software}
            <div class="col-md-{$col_size}">
                <h3>{__('2nd section', 'The software')}</h3>

                <p class="text-center" role="presentation">
                    <span class="glyphicon glyphicon-cloud" style="font-size:50px"></span>
                </p>

                <p>{__('2nd section', 'Framadate was initially based on ')}
                    <a href="https://sourcesup.cru.fr/projects/studs/">Studs</a>
                    {__('2nd section', 'a software developed by the University of Strasbourg. Today, it is devevoped by the association Framasoft.')}
                </p>

                <p>{__('2nd section', 'This software needs javascript and cookies enabled. It is compatible with the following web browsers:')}</p>
                <ul>
                    <li>Microsoft Internet Explorer 9+</li>
                    <li>Google Chrome 19+</li>
                    <li>Firefox 12+</li>
                    <li>Safari 5+</li>
                    <li>Opera 11+</li>
                </ul>
                <p>
                    {__('2nd section', 'It is governed by the')}
                    <a href="http://www.cecill.info">{__('2nd section', 'CeCILL-B license')}</a>.
                </p>
            </div>
        {/if}
        {if $show_cultivate_your_garden}
            <div class="col-md-{$col_size}">
                <h3>{__('3rd section', 'Cultivate your garden')}</h3>

                <p class="text-center" role="presentation">
                    <span class="glyphicon glyphicon-tree-deciduous" style="font-size:50px"></span>
                </p>

                <p>
                    {__('3rd section', 'To participate in the software development, suggest improvements or simply download it, please visit ')}
                    <a href="https://framagit.org/framasoft/framadate">{__('3rd section', 'the development site')}</a>.
                </p>
                <br/>

                <p>{__('3rd section', 'If you want to install the software for your own use and thus increase your independence, we help you on:')}</p>

                <p class="text-center">
                    <a href="http://framacloud.org/cultiver-son-jardin/installation-de-framadate/"
                       class="btn btn-success">
                        <span class="glyphicon glyphicon-tree-deciduous"></span>
                        framacloud.org
                    </a>
                </p>
            </div>
        {/if}
    </div>
{/block}
