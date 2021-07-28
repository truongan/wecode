@php($selected = 'settings')
@extends('layouts.app')
@section('head_title','Submission queue')
@section('icon', 'fas fa-play')

@section('title', 'Submission queue')

@section('content')

<div class="mx-n2">
  <p>
    Total submissions in queue: {{$queue->count()}}
  </p>
  <div style="float: left;" class="pe-1">
    <form action="{{ route('queue.work') }}" method="POST">
      @csrf
      <button href="#" class="shj_act btn btn-primary" id="spawn" data-bs-toggle="tooltip" data-placement="right" title="A queue processor process is spawned every time there is a submission or rejudging request. You can manually spawn one with this link" type="submit"><i class="fa fa-play"></i> Spawn new queue process </button>
    </form>
  </div>
  <div style="float: left;" class="pb-3">
    <form action="{{ route('queue.empty') }}" method="POST">
      @csrf
      <button href="#" class="shj_act btn btn-danger" id="empty_queue"  data-bs-toggle="tooltip" data-placement="right" title="Empty the queue, all queue processor process should exit on their own, leaving submission in PENDING state"><i class="fa fa-times-circle"></i> Empty Queue</button>
    </form>
  </div>
  <table class="wecode_table table table-striped table-bordered">
    <thead class="thead-old table-dark">
    <tr>
      <th>id</th>
      <th>Submit ID</th>
      <th>Usename</th>
      <th>Assignment</th>
      <th>Problem</th>
      <th>Type (judge/rejudge)</th>
      <th>Process PID</th>
      <th><i class="fas fa-toolbox"></i></th>
    </tr>
    </thead>
      @foreach ($queue as $item)
      <tr>
        <td>{{ $item->id }}</td>
        <td>{{ $item->submission_id }}</td>
        <td>{{ $item->submission->user->username }}</td>
        <td>{{ $item->submission->assignment->id }} (<span>{{ $item->submission->assignment->name }}</span>)</td>
        <td>{{ $item->submission->problem->id }}</td>
        <td>{{ $item->type }}</td>
        <td>{{ $item->processid }}</td>
        <td>
          @if ($item->processid)
            <form action="{{ route('queue.unlock', $item->id) }}" method="POST" >
              @csrf
              <button href="#" type="submit" class="shj_act btn btn-danger" id="unlock/{{ $item->id }}"  data-bs-toggle="tooltip" data-placement="right" title="Unlock this queue item, allow it to be processed. Should only be used if its processor process has terminated somehow. MUST DOUBLE CHECK BEFORE USE"><i class="fas fa-lock-open"></i></button>
            </form>
          @endif
        </td>
      </tr>
      @endforeach
  </table>
</div>
@endsection

