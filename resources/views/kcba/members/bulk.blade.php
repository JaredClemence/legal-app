<html>
    <head></head>
    <body>
        <h1>Bulk Create Bar Members</h1>
        <form action="{{route("kcba.bulk.create")}}" method="post">
            @csrf
            <table>
                <tr>
                    <th>Line</th>
                    <th>Name</th>
                    <th>Firm</th>
                    <th>Email</th>
                    <th>Barnum</th>
                </tr>
            @for($i=0; $i<50; $i++)
                <tr>
                    <td>{{($i+1)}}</td>
                    <td>
                        <input name="name_{{$i}}" id="name_{{$i}}" class="form-control" />
                    </td>
                    <td>
                        <input name="firm_{{$i}}" id="firm_{{$i}}" class="form-control" />
                    </td>
                    <td>
                        <input name="email_{{$i}}" id="email_{{$i}}" class="form-control" />
                    </td>
                    <td>
                        <input name="barnum_{{$i}}" id="barnum_{{$i}}" class="form-control" />
                    </td>
                </tr>
            @endfor
            <tr>
                <td colspan="5"><input type="submit" /></td>
            </tr>
            </table>
            
        </form>
    </body>
</html>

