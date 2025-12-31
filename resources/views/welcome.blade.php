<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIGEA - Sistema Integrado de Gestión Empresarial</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            background: #005cbf;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: white !important;
        }

        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            margin: 0 15px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }

        .btn-login {
            background: white;
            color: #005cbf;
            padding: 8px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: #005cbf;
            text-decoration: none;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #005cbf 0%, #003d8f 100%);
            color: white;
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,101.3C1248,85,1344,75,1392,69.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            opacity: 0.3;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            animation: fadeInUp 1s;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            opacity: 0.95;
            animation: fadeInUp 1s 0.2s backwards;
        }

        .hero .btn-primary {
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 30px;
            background: white;
            color: #005cbf;
            border: none;
            font-weight: 600;
            transition: all 0.3s;
            animation: fadeInUp 1s 0.4s backwards;
        }

        .hero .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            background: #f8f9fa;
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .section-title p {
            font-size: 1.1rem;
            color: #666;
        }

        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s;
            height: 100%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, #005cbf 0%, #003d8f 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }

        .feature-card h4 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Products Section */
        .products {
            padding: 80px 0;
        }

        .product-item {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }

        .product-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .product-item i {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #005cbf 0%, #003d8f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

        .product-item h5 {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        /* About Section */
        .about {
            padding: 80px 0;
            background: linear-gradient(135deg, #005cbf 0%, #003d8f 100%);
            color: white;
        }

        .about h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .about p {
            font-size: 1.1rem;
            line-height: 1.8;
            opacity: 0.95;
        }

        .stats {
            margin-top: 50px;
        }

        .stat-item {
            text-align: center;
            padding: 30px;
        }

        .stat-item h3 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-item p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Footer */
        footer {
            background: #6c757d;
            color: white;
            padding: 50px 0 20px;
        }

        footer h5 {
            font-weight: 600;
            margin-bottom: 20px;
        }

        footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
        }

        footer a:hover {
            color: white;
            padding-left: 5px;
        }

        footer .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            transition: all 0.3s;
        }

        footer .social-links a:hover {
            background: #005cbf;
            transform: translateY(-3px);
        }

        .copyright {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 40px;
            padding-top: 30px;
            text-align: center;
            color: rgba(255,255,255,0.6);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section-title h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-water mr-2"></i>SIGEA
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#servicios">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#productos">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#nosotros">Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn-login" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt mr-2"></i>Ingresar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1>Sistema Integrado de Gestión Empresarial</h1>
                    <p>Optimiza tu aguatería con SIGEA: gestión completa de ventas, compras, inventario y servicios en una sola plataforma.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-rocket mr-2"></i>Comenzar Ahora
                    </a>
                </div>
                <div class="col-lg-6 text-center d-none d-lg-block">
                    <i class="fas fa-desktop" style="font-size: 15rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="servicios">
        <div class="container">
            <div class="section-title">
                <h2>Nuestros Servicios</h2>
                <p>Soluciones integrales para la gestión de tu empresa</p>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h4>Módulo de Compras</h4>
                        <p>Gestión completa de proveedores, órdenes de compra, recepciones y control de pagos.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <h4>Módulo de Ventas</h4>
                        <p>Facturación electrónica, cobranzas, cuentas por cobrar y reportes de ventas detallados.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h4>Control de Stock</h4>
                        <p>Inventario en tiempo real, ajustes de stock, trazabilidad y alertas de stock mínimo.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h4>Gestión de Servicios</h4>
                        <p>Órdenes de servicio, mantenimientos, reclamos y seguimiento completo de atención al cliente.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products" id="productos">
        <div class="container">
            <div class="section-title">
                <h2>Productos Destacados</h2>
                <p>Amplia variedad de productos para distribución</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="product-item">
                        <i class="fas fa-tint"></i>
                        <h5>Agua Purificada</h5>
                        <p>Bidones de 20 litros de agua purificada, tratada con tecnología de ósmosis inversa y ozonización.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="product-item">
                        <i class="fas fa-wine-bottle"></i>
                        <h5>Dispensers y Accesorios</h5>
                        <p>Dispensers fríos, calientes y a temperatura ambiente. Incluye soportes, vasos y accesorios.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="product-item">
                        <i class="fas fa-battery-three-quarters"></i>
                        <h5>Productos Complementarios</h5>
                        <p>Filtros, bombas dispensadoras, tapas herméticas y productos de limpieza especializados.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="product-item">
                        <i class="fas fa-truck"></i>
                        <h5>Servicio de Entrega</h5>
                        <p>Entrega a domicilio programada, servicio express y distribución para empresas.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="product-item">
                        <i class="fas fa-recycle"></i>
                        <h5>Programa de Reciclaje</h5>
                        <p>Sistema de retorno de bidones vacíos, contribuyendo al cuidado del medio ambiente.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="product-item">
                        <i class="fas fa-handshake"></i>
                        <h5>Planes Corporativos</h5>
                        <p>Soluciones a medida para empresas, oficinas y eventos con tarifas preferenciales.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="nosotros">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2>Sobre Nuestra Empresa</h2>
                    <p>
                        Somos una empresa líder en la distribución de agua purificada y productos relacionados,
                        comprometida con la salud y bienestar de nuestros clientes.
                    </p>
                    <p>
                        Con más de 15 años de experiencia en el mercado, nos destacamos por ofrecer productos
                        de la más alta calidad, cumpliendo con todas las normativas sanitarias vigentes.
                        Nuestro proceso de purificación incluye filtración múltiple, ósmosis inversa y
                        ozonización, garantizando agua 100% pura y segura.
                    </p>
                    <p>
                        Nuestra misión es brindar un servicio de excelencia, con entregas puntuales,
                        atención personalizada y compromiso con el medio ambiente a través de nuestro
                        programa de reciclaje de envases.
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="stats">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <h3><i class="fas fa-users"></i> 5,000+</h3>
                                    <p>Clientes Satisfechos</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <h3><i class="fas fa-tint"></i> 50,000+</h3>
                                    <p>Bidones Distribuidos/Mes</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <h3><i class="fas fa-truck"></i> 15+</h3>
                                    <p>Vehículos de Reparto</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <h3><i class="fas fa-map-marker-alt"></i> 8</h3>
                                    <p>Zonas de Cobertura</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="fas fa-water mr-2"></i>SIGEA</h5>
                    <p>Sistema Integrado de Gestión Empresarial para aguaterías. Optimiza tu negocio con tecnología de vanguardia.</p>
                    <div class="social-links mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled">
                        <li><a href="#inicio">Inicio</a></li>
                        <li><a href="#servicios">Servicios</a></li>
                        <li><a href="#productos">Productos</a></li>
                        <li><a href="#nosotros">Nosotros</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Servicios</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Gestión de Compras</a></li>
                        <li><a href="#">Gestión de Ventas</a></li>
                        <li><a href="#">Control de Stock</a></li>
                        <li><a href="#">Órdenes de Servicio</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 mb-4">
                    <h5>Contacto</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt mr-2"></i>Asunción, Paraguay</li>
                        <li><i class="fas fa-phone mr-2"></i>+595 21 123 4567</li>
                        <li><i class="fas fa-envelope mr-2"></i>info@sigea.com.py</li>
                        <li><i class="fas fa-clock mr-2"></i>Lun - Sáb: 7:00 - 19:00</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 SIGEA - Sistema Integrado de Gestión Empresarial. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Smooth scrolling
        $(document).ready(function(){
            $('a[href^="#"]').on('click',function (e) {
                e.preventDefault();
                var target = this.hash;
                var $target = $(target);
                $('html, body').stop().animate({
                    'scrollTop': $target.offset().top - 70
                }, 900, 'swing');
            });

            // Navbar background on scroll
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                } else {
                    $('.navbar').removeClass('scrolled');
                }
            });
        });
    </script>
</body>
</html>
