@php
use Illuminate\Support\Facades\Vite;
@endphp

<!-- laravel style -->
@vite(['resources/assets/vendor/js/helpers.js'])
<!-- beautify ignore:start -->
@if ($configData['hasCustomizer'])
  {{-- Template customizer dimatikan untuk front layout --}}
@endif

  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
  @vite(['resources/assets/js/front-config.js'])

@if ($configData['hasCustomizer'])
<script type="module">
    if (typeof TemplateCustomizer !== 'undefined') {
      window.templateCustomizer = new TemplateCustomizer({
        cssPath: '',
        themesPath: '',
        defaultStyle: "{{$configData['styleOpt']}}",
        displayCustomizer: "{{$configData['displayCustomizer']}}",
        pathResolver: function(path) {
          var resolvedPaths = {
            // Core stylesheets
            @foreach (['core'] as $name)
              '{{ $name }}.scss': '{{ Vite::asset('resources/assets/vendor/scss'.$configData["rtlSupport"].'/'.$name.'.scss') }}',
              '{{ $name }}-dark.scss': '{{ Vite::asset('resources/assets/vendor/scss'.$configData["rtlSupport"].'/'.$name.'-dark.scss') }}',
            @endforeach

            // Themes
            @foreach (['default', 'bordered', 'semi-dark'] as $name)
              'theme-{{ $name }}.scss': '{{ Vite::asset('resources/assets/vendor/scss'.$configData["rtlSupport"].'/theme-'.$name.'.scss') }}',
              'theme-{{ $name }}-dark.scss': '{{ Vite::asset('resources/assets/vendor/scss'.$configData["rtlSupport"].'/theme-'.$name.'-dark.scss') }}',
            @endforeach
          }
          return resolvedPaths[path] || path;
        },
        'controls': <?php echo json_encode(['rtl', 'style']); ?>,

      });
    } else {
      console.warn('TemplateCustomizer script not loaded; customizer disabled.');
    }
  </script>
@endif
