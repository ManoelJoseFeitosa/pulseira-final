<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autism Watch - Monitoramento Inteligente para o Espectro Autista</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <header class="bg-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Autism Watch" class="h-48 mr-3">
            </div>
            <div class="space-x-4">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-500">Acessar</a>
                <a href="{{ route('register') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full transition duration-300">Registrar</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="text-center py-20 px-6 bg-white">
            <div class="container mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-blue-600 mb-4">Mais Segurança e Compreensão no Dia a Dia.</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-8">Autism Watch é a pulseira inteligente que traduz sinais vitais em insights valiosos, oferecendo tranquilidade para cuidadores e mais autonomia para quem está no espectro autista.</p>
                <a href="{{ route('register') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300">Comece a Monitorar Agora</a>
            </div>
        </section>

        <section class="py-20 px-6">
            <div class="container mx-auto">
                <h2 class="text-3xl font-bold text-center mb-12">Uma Inovação que Cuida</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="bg-white p-8 rounded-lg shadow-md text-center">
                        <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Alertas Proativos</h3>
                        <p class="text-gray-600">Receba notificações por WhatsApp ou SMS quando os níveis de estresse ou agitação mudam, permitindo uma intervenção rápida e gentil.</p>
                    </div>
                    <div class="bg-white p-8 rounded-lg shadow-md text-center">
                         <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Compreensão Profunda</h3>
                        <p class="text-gray-600">Nossos gráficos e relatórios ajudam a identificar padrões e possíveis gatilhos de crises, transformando dados em conhecimento e cuidado.</p>
                    </div>
                    <div class="bg-white p-8 rounded-lg shadow-md text-center">
                         <div class="bg-yellow-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Tranquilidade para a Família</h3>
                        <p class="text-gray-600">Acompanhe os sinais vitais em tempo real de onde estiver, sabendo que você será o primeiro a ser avisado em caso de qualquer alteração importante.</p>
                    </div>
                    <div class="bg-white p-8 rounded-lg shadow-md text-center">
                         <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Mais Autonomia e Segurança</h3>
                        <p class="text-gray-600">Promova a independência com a confiança de que um anjo da guarda digital está sempre presente, de forma discreta e confortável.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-white mt-12">
        <div class="container mx-auto px-6 py-8 text-center text-gray-600">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Autism Watch" class="h-16 mx-auto mb-4">
            <p>&copy; 2025 Autism Watch. Todos os direitos reservados.</p>
            <p>Conectando tecnologia e empatia para um futuro mais seguro.</p>
        </div>
    </footer>

</body>
</html>
