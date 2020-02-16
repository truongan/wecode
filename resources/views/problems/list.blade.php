@extends('layouts.app')
@php($selected="settings")
@section('icon', 'fas fa-clipboard-list')

@section('title','Problems List')

@section('title_menu')
{{-- {% if user.level >= 2 %} --}}
<span class="title_menu_item"><a href="{{ url('problems/create') }}"><i class="fas fa-plus fa-lg color8"></i> Add</a></span>
<span class="title_menu_item"><a href="{{ url('submissions/all/assignments/0') }}"><i class="fas fa-list-ul fa-lg color8"></i>Review test submissions for problems</a></span>
<span class="title_menu_item"><a href="{{ url('problems/download_all') }}"><i class="fas fa-download fa-lg color8"></i>Download all problem's test and description</a></span>
@endsection

@section('content')
<div class="col">
    {{-- {% set msgclasses = {'success': 'text-success', 'notice': 'text-info', 'error': 'text-danger'} %}
    {% for message in messages %}
        <p class="{{ msgclasses[message.type] }} {{message.type}}">{{ message.text }}</p>
    {% endfor %}
     --}}
    {{-- {% if all_problems|length == 0 %}
        <p style="text-align: center;">Nothing to show...</p>
    {% else %} --}}
    <table class="wecode_table table table-striped table-bordered">
    <thead class="thead-dark">
    <tr>
        <th>ID</th>
        <th style="width: 20%">Name</th>
        <th style="width: 20%">Note</th>
        <th>Languages</th>
        <th>Used in assignmnets</th>
        <th>diff<br/>command</th>
        <th>diff<br/>argument</th>
        <th>Tools</th>
    </tr>
    </thead>
    @foreach ($problems as $item)
    <tr>
        
        <td>{{ $item->id}}</td>
        <td><a href="{{ url("problems/show/$item->id") }}">{{ $item->name }}</a></td>
        <td>{{$item->admin_note}}</td>
        <td>
        @foreach ($item->get_id as $language_name)
            {{$language_name->name}}
        @endforeach
        </td>
        <td>
            {{-- {% for ass_id in item.assignments %}
                <a href="{{ site_url("assignments/edit/#{ass_id}") }}" class="badge badge-primary">asgmt {{ ass_id}}</a>
            {% endfor %} --}}
        </td>
        <td>{{ $item->diff_cmd }}</td>
        <td>{{ $item->diff_arg }}</td>
        
        <td> 
            <form method="POST"  action="{!! route('problems.pdf',$item->id) !!}">
                <input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
                <button title="Download Tests and Descriptions" class="fa fa-cloud-download-alt fa-lg color11">
            </form>
            {{-- {# <a href="#"><i title="Download Final Submissions (by user)" class="fa fa-download fa-lg color12"></i></a> #}
            {# <a href="#"><i title="Download Final Submissions (by problem)" class="fa fa-download fa-lg color2"></i></a> #} --}}
            {{-- <a href="#"><i title="Edit" class="far fa-edit fa-lg color3"></i></a>
            
            <a href="#"><i title="Delete" class="far fa-trash-alt fa-lg color1"></i></a> --}}
            
        </td>
    
    </tr>
    @endforeach
    </table>
</div>
@endsection