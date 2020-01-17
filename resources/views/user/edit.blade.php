{!!Form::model($user, array('route' => array('users.update', $user->id), 'method' => 'PUT')) !!}

    <div class="form-group">
        {!! Form::label('username', 'Mã Số Sinh Viên', ['class'=>'control-label col-md-2']) !!}
        <div class="col-md-4">
          {!! Form::text('username', null, ['class' => 'form-control']) !!}
        </div>

      </div>

   {!! Form::submit('Chỉnh Sửa', array('class' => 'btn btn-primary')) !!}
   

{!! Form::close() !!}