@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
          <div class="panel-heading">Settings</div>

          <div class="panel-body">
            @if (session('status'))
              <div class="alert alert-success">
                {{ session('status') }}
              </div>
            @endif

            <form method="post">
              {{ csrf_field() }}
              <!-- Url Field -->
              <fieldset class="form-group">
                <label for="bot-url">Bot URL:</label>
                <input type="url" id="bot-url" name="url" value="{{ $url }}" class="form-control">
              </fieldset>

              <!-- Telegram Token Field -->
              <fieldset class="form-group">
                <label for="telegram-token">Telegram Token:</label>
                <input type="text" id="telegram-token" name="token" value="{{ $token }}" class="form-control">
              </fieldset>

              <!-- Default Currency Field -->
              <fieldset class="form-group">
                <label for="currency">Default Currency:</label>
                <select id="currency" name="currency" class="form-control">
                  @foreach($currencies as $c)
                    <option value="{{ $c->currency }}"{{ $c->currency === $currency ? ' selected' : '' }}>
                      {{ $c->currency }} ({{ $c->country }})
                    </option>
                  @endforeach
                </select>
              </fieldset>


              <!-- Submit Button -->
              <fieldset class="form-group">
                <input type="submit" name="submit" value="Submit" class="btn btn-primary">
              </fieldset>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
