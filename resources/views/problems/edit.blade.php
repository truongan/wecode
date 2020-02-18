<form action="/problems/edit_description/{{ $notification->id }}" method="POST">
    @method('PUT')
    <input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
    <input type="hidden" name="id" value="{{ $notification->id }}"/>

    <p class="input_p">
        <label for="form_title" class="tiny">Title:</label>
        <input id="form_title" name="title" type="text" class="sharif_input" value=" {{$notification->title}} "/>
    </p>
    <p class="input_p">
        <label for="notif_text" class="tiny">Text:</label><br><br>
        <textarea id="notif_text" name="text"> {!!$notification->text!!} </textarea>
    </p>
    <p class="input_p">
        <input type="submit" value="Save" class="sharif_input"/>
    </p>
</form>	
@endsection