<!-- Footer -->
<footer class="footer" id="contact">
    <div class="container">
        <div class="footer-main">
            <div class="footer-col">
                <a href="{{ route('welcome') }}" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-atom"></i>
                    </div>
                    <div class="logo-text">
                        <span class="logo-main">Research</span>
                        <span class="logo-sub">Portal</span>
                    </div>
                </a>
                <p class="footer-description">
                    {!! \App\Models\SiteContent::getByKey('footer_description', 'A premier platform for academic research submission, peer review, and open-access publication. Advancing knowledge through collaboration and innovation.') !!}
                </p>
                <div class="social-links">
                    @php
                        $twitter = \App\Models\SiteContent::getByKey('social_twitter');
                        $linkedin = \App\Models\SiteContent::getByKey('social_linkedin');
                        $youtube = \App\Models\SiteContent::getByKey('social_youtube');
                        $github = \App\Models\SiteContent::getByKey('social_github');
                        $orcid = \App\Models\SiteContent::getByKey('social_orcid');
                    @endphp
                    @if($twitter)
                    <a href="{{ $twitter }}" class="social-link" aria-label="Twitter" target="_blank">
                        <i class="fab fa-twitter"></i>
                    </a>
                    @endif
                    @if($linkedin)
                    <a href="{{ $linkedin }}" class="social-link" aria-label="LinkedIn" target="_blank">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    @endif
                    @if($youtube)
                    <a href="{{ $youtube }}" class="social-link" aria-label="YouTube" target="_blank">
                        <i class="fab fa-youtube"></i>
                    </a>
                    @endif
                    @if($github)
                    <a href="{{ $github }}" class="social-link" aria-label="GitHub" target="_blank">
                        <i class="fab fa-github"></i>
                    </a>
                    @endif
                    @if($orcid)
                    <a href="{{ $orcid }}" class="social-link" aria-label="ORCID" target="_blank">
                        <i class="fab fa-orcid"></i>
                    </a>
                    @endif
                </div>
            </div>
            <div class="footer-col">
                <h3 class="footer-title">Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('welcome') }}"><i class="fas fa-chevron-right"></i> Home</a></li>
                    <li><a href="{{ route('publications.index') }}"><i class="fas fa-chevron-right"></i> Publications</a></li>
                    @guest
                    <li><a href="{{ route('login') }}"><i class="fas fa-chevron-right"></i> Login</a></li>
                    <li><a href="{{ route('register') }}"><i class="fas fa-chevron-right"></i> Register</a></li>
                    @endguest
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Submission Guidelines</a></li>
                </ul>
            </div>
{{--            <div class="footer-col">--}}
{{--                <h3 class="footer-title">Resources</h3>--}}
{{--                <ul class="footer-links">--}}
{{--                    <li><a href="#"><i class="fas fa-chevron-right"></i> Author Resources</a></li>--}}
{{--                    <li><a href="#"><i class="fas fa-chevron-right"></i> Reviewer Guidelines</a></li>--}}
{{--                    <li><a href="#"><i class="fas fa-chevron-right"></i> Editorial Board</a></li>--}}
{{--                    <li><a href="#"><i class="fas fa-chevron-right"></i> Publication Ethics</a></li>--}}
{{--                    <li><a href="#"><i class="fas fa-chevron-right"></i> Research Tools</a></li>--}}
{{--                </ul>--}}
{{--            </div>--}}
            <div class="footer-col">
                <h3 class="footer-title">Contact Us</h3>
                <ul class="footer-contact">
                    @php
                        $address = \App\Models\SiteContent::getByKey('footer_address');
                        $phone = \App\Models\SiteContent::getByKey('footer_phone');
                        $email = \App\Models\SiteContent::getByKey('footer_email');
                        $hours = \App\Models\SiteContent::getByKey('footer_hours');
                    @endphp
                    @if($address)
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{!! nl2br(e($address)) !!}</span>
                    </li>
                    @endif
                    @if($phone)
                    <li>
                        <i class="fas fa-phone"></i>
                        <span><a href="tel:{{ $phone }}">{{ $phone }}</a></span>
                    </li>
                    @endif
                    @if($email)
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span><a href="mailto:{{ $email }}">{{ $email }}</a></span>
                    </li>
                    @endif
                    @if($hours)
                    <li>
                        <i class="fas fa-clock"></i>
                        <span>{{ $hours }}</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {!! \App\Models\SiteContent::getByKey('footer_copyright', 'Academic Research Portal. All rights reserved.') !!}</p>
            <div class="footer-legal">
                @php
                    $privacy = \App\Models\SiteContent::getByKey('footer_privacy_link');
                    $terms = \App\Models\SiteContent::getByKey('footer_terms_link');
                    $cookie = \App\Models\SiteContent::getByKey('footer_cookie_link');
                @endphp
                @if($privacy)
                <a href="{{ $privacy }}">Privacy Policy</a>
                @endif
                @if($terms)
                <a href="{{ $terms }}">Terms of Service</a>
                @endif
                @if($cookie)
                <a href="{{ $cookie }}">Cookie Policy</a>
                @endif
            </div>
        </div>
    </div>
</footer>
