<!doctype html>
<html lang="ar">

<head>
    <title>تسجيل الدخول</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets\admin\img\logo.jpg')}}">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="{{ URL::asset('assets\admin\login\css\style.css') }}">

</head>

<body style="direction:rtl;">
    <section class="ftco-section">
        <div class="container">
            {{-- <div class="row justify-content-center">
                <div class="col-md-6 text-center mb-5">
                    <h2 class="heading-section">{{ __('login.alhamd') }}</h2>
                </div>
            </div> --}}
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="login-wrap p-4 p-md-5">
                        <div class="icon d-flex align-items-center justify-content-center">
                            <img src="{{asset('assets\admin\img\alhamd.jpg')}}" alt="" style="width:100%; border-radius:50%; border:1px solid;">
                        </div>
                        {{-- <h3 class="text-center mb-4">Have an account?</h3> --}}
                        <form action="{{ route('login') }}" method="POST" class="login-form">
                            @csrf
                            <div class="form-group">
                                <input type="email" name="email" class="form-control rounded-left" placeholder="الإيميل" value="ahmed@gmail.com" required>
                            </div>
                            <div class="form-group d-flex">
                                <input type="password" name="password" class="form-control rounded-left" placeholder="كلمه المرور" value="2022" required>
                            </div>
                            <div class="form-group d-md-flex">
                                {{-- <div>
                                    <label class="checkbox-wrap checkbox-primary">تذكرنى
                                        <input type="checkbox" checked>
                                        <span class="checkmark"></span>
                                    </label>
                                </div> --}}
                                {{-- <div class="w-50 text-md-right">
                                    <a href="#">Forgot Password</a>
                                </div> --}}
                                @error('email')
                                    <p class="alert alert-danger" style="width:100%; margin-bottom:0px; text-align:center;">يرجا التأكد من الإيميل وكلمه المرور</p>
                                @enderror
                            </div>

                            {{--  --}}
                            {{--  --}}


                                {{-- userName && password الخاصه بالحقول class يأخذ نفس إسم div هذا  --}}
                                {{-- <div class="form-group d-md-flex"> --}}

                                    {{-- فى المتصفحrecaptch هذا السطر الذى يقوم بإظهار  --}}
                                    {{-- {!! NoCaptcha::display(['data-theme' => 'dark']) !!} --}}



                                {{-- </div> --}}


                                {{-- <div class="form-group d-md-flex"> --}}
                                    {{-- يقوم بطباعتها recaptcha تقوم باتحقق إذا كان يوجد إخطاء خاصه ب if هذه  --}}
                                    {{-- @if ($errors->has('g-recaptcha-response')) --}}

                                        {{-- <p class="alert alert-danger" style="width:100%; margin-bottom:0px; text-align:center;">{{ $errors->first('g-recaptcha-response') }}</p> --}}

                                    {{-- @endif --}}
                                {{-- </div> --}}

                            {{--  --}}
                            {{--  --}}
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary rounded submit p-3 px-5">تسجيل الدخول</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ URL::asset('assets\admin\login\js\jquery.min.js') }}"></script>
    <script src="{{ URL::asset('assets\admin\login\js\popper.js') }}"></script>
    <script src="{{ URL::asset('assets\admin\login\js\bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('assets\admin\login\js\main.js') }}"></script>

    {{-- أم لا recaptcha هذا السطر يقوم بالتأكد أن المتصفح يدعم  --}}
    {{-- {!! NoCaptcha::renderJs() !!}

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> --}}

</body>

</html>
