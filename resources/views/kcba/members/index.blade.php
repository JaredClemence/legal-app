<html>
    <head></head>
    <body>
        <h1>SUCCESS</h1>
        <table>
        @foreach($members as $member)
    <tr>
        <td>{{$member->user->name}}</td>
    </tr>
        @endforeach
        </table>
    </body>
</html>
