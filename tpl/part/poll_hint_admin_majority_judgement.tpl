{extends file="part/poll_hint.tpl"}

{block name = "vote_options"}
    <p aria-hidden="true">
    <b>{__('studs', 'Vote according to your preferences.')}</b>
    <b>{__('Generic', 'Legend:')}</b>
    {__('Generic', 'Excellent')} <b> > </b>
    {__('Generic', 'Good')} <b> > </b>
    {__('Generic', 'Fair')} <b> > </b>
    {__('Generic', 'Poor')} <b> > </b>
    {__('Generic', 'To-reject')}</p>
{/block}