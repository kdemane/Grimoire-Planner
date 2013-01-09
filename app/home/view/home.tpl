WELCOME TO GRIMOIRE PLANNER
<br />
<br />

{if $smarty.const.USERID}
Welcome, $smarty.const.USERNAME
{else}
Log in: {include file='user/view/login.tpl'}
{/if}
