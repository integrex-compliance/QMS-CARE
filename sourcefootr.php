<!----------- Modern Footer ------------>
<footer class="modern-footer">
    <div class="footer-content">
        <div class="footer-main">
            <div class="footer-brand">
                <h3 class="footer-logo">QMS Care</h3>
                <p class="footer-tagline">Leading ISO Consultants Delivering 100% Customized Quality Management Solutions</p>
            </div>
            
            <div class="footer-links">
                <div class="footer-column">
                    <h4 class="footer-title">Company</h4>
                    <ul class="footer-list">
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="service.php">Services</a></li>
                        <li><a href="#">Our Team</a></li>
                        <li><a href="#">Careers</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4 class="footer-title">Services</h4>
                    <ul class="footer-list">
                        <li><a href="#">ISO Certification</a></li>
                        <li><a href="#">Consulting</a></li>
                        <li><a href="#">Training</a></li>
                        <li><a href="#">Audit Services</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4 class="footer-title">Contact</h4>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Ramnagar Complex, Bhosari<br>Pune - 411 039</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:qmscare@gmail.com">qmscare@gmail.com</a>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>7774034698 / 9689882677</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p class="footer-copyright">© 2024 Quality Management Certification. All rights reserved.</p>
                <div class="footer-social">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* ========================================
   MODERN FOOTER DESIGN
   ======================================== */

.modern-footer {
    background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%) !important;
    color: #e2e8f0 !important;
    margin-top: 120px !important;
    width: 100vw !important;
    position: relative !important;
    left: 50% !important;
    right: 50% !important;
    margin-left: -50vw !important;
    margin-right: -50vw !important;
}

.footer-content {
    max-width: 1400px !important;
    margin: 0 auto !important;
    padding: 0 80px !important;
}

.footer-main {
    display: grid !important;
    grid-template-columns: 2fr 3fr !important;
    gap: 120px !important;
    padding: 100px 0 70px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}

/* Footer Brand */
.footer-brand {
    max-width: 480px !important;
}

.footer-logo {
    font-size: 36px !important;
    font-weight: 900 !important;
    color: #ffffff !important;
    margin: 0 0 24px 0 !important;
    letter-spacing: -0.02em !important;
    line-height: 1 !important;
}

.footer-tagline {
    font-size: 17px !important;
    line-height: 1.7 !important;
    color: #94a3b8 !important;
    margin: 0 !important;
}

/* Footer Links */
.footer-links {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 80px !important;
}

.footer-column {
    display: flex !important;
    flex-direction: column !important;
}

.footer-title {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    margin: 0 0 28px 0 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
}

.footer-list {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 16px !important;
}

.footer-list li a {
    color: #94a3b8 !important;
    text-decoration: none !important;
    font-size: 16px !important;
    transition: all 0.2s ease !important;
    display: inline-block !important;
}

.footer-list li a:hover {
    color: #ffffff !important;
    transform: translateX(6px) !important;
}

/* Footer Contact */
.footer-contact {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 20px !important;
}

.footer-contact li {
    display: flex !important;
    gap: 16px !important;
    align-items: flex-start !important;
    color: #94a3b8 !important;
    font-size: 16px !important;
    line-height: 1.6 !important;
}

.footer-contact li i {
    color: #3b82f6 !important;
    font-size: 18px !important;
    margin-top: 4px !important;
    flex-shrink: 0 !important;
    width: 22px !important;
}

.footer-contact li a {
    color: #94a3b8 !important;
    text-decoration: none !important;
    transition: color 0.2s ease !important;
}

.footer-contact li a:hover {
    color: #ffffff !important;
}

/* Footer Bottom */
.footer-bottom {
    padding: 40px 0 !important;
}

.footer-bottom-content {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 24px !important;
}

.footer-copyright {
    font-size: 15px !important;
    color: #64748b !important;
    margin: 0 !important;
}

/* Social Links */
.footer-social {
    display: flex !important;
    gap: 14px !important;
}

.social-link,
.footer-social a,
a.social-link {
    width: 48px !important;
    height: 48px !important;
    background: rgba(255, 255, 255, 0.05) !important;
    border: none !important;
    border-radius: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #94a3b8 !important;
    text-decoration: none !important;
    font-size: 18px !important;
    transition: all 0.3s ease !important;
    outline: none !important;
    box-shadow: none !important;
}

.social-link:hover,
.footer-social a:hover,
a.social-link:hover {
    background: #3b82f6 !important;
    color: #ffffff !important;
    transform: translateY(-4px) !important;
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4) !important;
    border: none !important;
}

/* Responsive Design */
@media (max-width: 1400px) {
    .footer-content {
        padding: 0 60px !important;
    }
    
    .footer-main {
        gap: 100px !important;
    }
    
    .footer-links {
        gap: 60px !important;
    }
}

@media (max-width: 1200px) {
    .footer-content {
        padding: 0 40px !important;
    }
    
    .footer-main {
        gap: 80px !important;
    }
    
    .footer-links {
        gap: 50px !important;
    }
}

@media (max-width: 1024px) {
    .footer-main {
        grid-template-columns: 1fr !important;
        gap: 60px !important;
        padding: 80px 0 60px !important;
    }
    
    .footer-links {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 40px !important;
    }
}

@media (max-width: 768px) {
    .footer-content {
        padding: 0 24px !important;
    }
    
    .modern-footer {
        margin-top: 80px !important;
    }
    
    .footer-main {
        padding: 60px 0 40px !important;
        gap: 50px !important;
    }
    
    .footer-links {
        grid-template-columns: 1fr !important;
        gap: 40px !important;
    }
    
    .footer-bottom {
        padding: 32px 0 !important;
    }
    
    .footer-bottom-content {
        flex-direction: column !important;
        text-align: center !important;
        gap: 20px !important;
    }
    
    .footer-social {
        justify-content: center !important;
    }
}

@media (max-width: 480px) {
    .footer-content {
        padding: 0 20px !important;
    }
    
    .footer-logo {
        font-size: 28px !important;
    }
    
    .footer-tagline {
        font-size: 15px !important;
    }
    
    .footer-main {
        padding: 50px 0 40px !important;
    }
    
    .footer-title {
        font-size: 13px !important;
        margin-bottom: 20px !important;
    }
    
    .footer-list li a,
    .footer-contact li {
        font-size: 15px !important;
    }
    
    .social-link {
        width: 44px !important;
        height: 44px !important;
        font-size: 16px !important;
    }
}
</style>
