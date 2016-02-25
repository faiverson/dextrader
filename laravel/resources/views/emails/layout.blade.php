<html>
<head>
	<title>{{ Config::get('dextrader.email.sender') }}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style type="text/css">{{ file_get_contents(app_path() . '/../public/email.css') }}</style>
</head>
<body>
<table id="background-table" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody>
	<tr>
        <td align="left">
			<table class="w640" border="0" cellpadding="0" cellspacing="0" width="640">
				<tbody>
				<tr class="large_only">
					<td class="w640" height="20" width="640"></td>
				</tr>
				<tr class="mobile_only">
					<td class="w640" height="10" width="640"></td>
				</tr>
				<tr class="mobile_only">
					<td class="w640" height="10" width="640"></td>
				</tr>
				<tr class="mobile_only">
					<td class="w640" width="640" align="center">
						<img class="mobile_only mobile-logo" border="0" src="{{ Config::get('dextrader.email.logo.path') }}" alt="{{ Config::get('dextrader.email.sender') or '' }}" width="{{ Config::get('dextrader.email.logo.width') }}" height="{{ Config::get('dextrader.email.logo.height') }}" />
					</td>
				</tr>
				<tr class="mobile_only">
					<td class="w640" height="20" width="640"></td>
				</tr>
				<tr class="large_only">
					<td class="w640"  height="20" width="640"></td>
				</tr>
				<tr>
					<td class="w640" width="640" colspan="3" height="20"></td>
				</tr>
				<tr class="mobile_only">
					<td class="w640"  height="10" width="640" bgcolor="#ffffff"></td>
				</tr>
				<tr class="mobile_only">
					<td class="w640"  height="20" width="640" bgcolor="#ffffff"></td>
				</tr>
				<tr>
					<td id="header" class="w640" align="center" width="640">
						<table class="w640" border="0" cellpadding="0" cellspacing="0" width="640">
							<tr>
								<td class="w30" width="30"></td>
								<td id="logo" width="{{ Config::get('dextrader.email.logo.width') }}" valign="top">
									<img border="0" src="{{ Config::get('dextrader.email.logo.path') }}" alt="{{ Config::get('dextrader.email.sender') }}" width="{{ Config::get('dextrader.email.logo.width') }}" height="{{ Config::get('dextrader.email.logo.height') }}" />
								</td>
								<td class="w30" width="30"></td>
							</tr>
							<tr>
								<td colspan="3" height="20" class="large_only"></td>
							</tr>
							<tr>
								<td colspan="3" height="20" class="large_only"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="w640" bgcolor="#ffffff" width="640">
						<table class="w640" border="0" cellpadding="0" cellspacing="0" width="640">
							<tbody>
							@section('content')
							@show
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td class="w640 footer" width="640" colspan="3" height="30">{{ Config::get('dextrader.email.sender') }} - All right reserved</td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
	</tbody>
</table>
</body>
</html>
