{HEADER}
<center>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="calborder">
		<tr>
			<td align="center" valign="middle" bgcolor="white">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td align="left" width="120" class="navback">
							&nbsp;
						</td>
						<td class="navback">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td align="right" width="45%" class="navback">
										<a class="psf" href="year.php?cal={CAL}&amp;getdate={PREV_YEAR}"><img src="templates/{TEMPLATE}/images/left_day.gif" alt="[Previous Year]" border="0" align="right" /></a>
									</td>
									<td align="center" width="10%" class="title" nowrap="nowrap" valign="middle">
										<h1>{THIS_YEAR}</h1>
									</td>
									<td align="left" width="45%" class="navback">
										<a class="psf" href="year.php?cal={CAL}&amp;getdate={NEXT_YEAR}"><img src="templates/{TEMPLATE}/images/right_day.gif" alt="[Next Year]" border="0" align="left" /></a>
									</td>
								</tr>
							</table>
						</td>
						<td align="right" width="120" class="navback">
							<table width="120" border="0" cellpadding="0" cellspacing="0">
								<tr>
								<td><a class="psf" href="day.php?cal={CAL}&amp;getdate={GETDATE}"><img src="templates/{TEMPLATE}/images/day_on.gif" alt="{L_DAY}" border="0" /></a></td>
								<td><a class="psf" href="week.php?cal={CAL}&amp;getdate={GETDATE}"><img src="templates/{TEMPLATE}/images/week_on.gif" alt="{L_WEEK}" border="0" /></a></td>
								<td><a class="psf" href="month.php?cal={CAL}&amp;getdate={GETDATE}"><img src="templates/{TEMPLATE}/images/month_on.gif" alt="{L_MONTH}" border="0" /></a></td>
								<td><a class="psf" href="year.php?cal={CAL}&amp;getdate={GETDATE}"><img src="templates/{TEMPLATE}/images/year_on.gif" alt="{L_YEAR}" border="0" /></a></td>
							</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<div class="year">
		<div class="monthlist">
			{MONTH_MEDIUM|01}
		</div>
		<div class="monthlist">
			{MONTH_MEDIUM|02}
		</div>
		<div class="monthlist">
			{MONTH_MEDIUM|03}
		</div>
		<div class="monthlist">
			{MONTH_MEDIUM|04}
		</div>
		<div class="monthlist">
			{MONTH_MEDIUM|05}
		</div>
		<div class="monthlist">
			{MONTH_MEDIUM|06}
		</div>
		<div class="monthlist">
			{MONTH_MEDIUM|07}
		</div>
		<div class="monthlist">
			{MONTH_MEDIUM|08}
		</div>
		<div class="monthlist">
			{MONTH_MEDIUM|09}
		</div>
		<div class="monthlist">
			{MONTH_MEDIUM|10}
		</div>
		<div class="monthlist">
			{MONTH_MEDIUM|11}
		</div>
		<div class="monthlist">
			{MONTH_MEDIUM|12}
		</div>
    </div>

</center>
{FOOTER}
