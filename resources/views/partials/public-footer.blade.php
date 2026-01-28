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
                    A premier platform for academic research submission, peer review, and open-access publication. Advancing knowledge through collaboration and innovation.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="ORCID">
                        <i class="fab fa-orcid"></i>
                    </a>
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
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>123 Research Avenue<br>ABCDSSSSS</span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>+14545454545</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>test@researchportal.edu</span>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span>Mon-Fri: 9:00 AM - 5:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Academic Research Portal. All rights reserved.</p>
            <div class="footer-legal">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
