# -Une-Taupe-Chez-Vous_Symfony
<nav class="relative px-4 py-4 flex justify-between items-center bg-white">
        <div class="lg:hidden">
          <button class="navbar-burger flex items-center text-blue-600 p-3">
            <svg class="block h-4 w-4 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <title>Mobile menu</title>
              <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"></path>
            </svg>
          </button>
        </div>
        <ul class="hidden absolute top-1/2 left-1/2 transform -translate-y-1/2 -translate-x-1/2 lg:flex lg:mx-auto lg:flex lg:items-center lg:w-auto lg:space-x-6">
          <li><a class="text-sm text-gray-400 hover:text-gray-500" href="#">Home</a></li>
          {% if is_granted('ROLE_USER') %}
          <li><a class="text-sm text-blue-600 font-bold" href="#">About Us</a></li>
          <li><a class="text-sm text-gray-400 hover:text-gray-500" href="#">Services</a></li>
          <li><a class="text-sm text-gray-400 hover:text-gray-500" href="#">Pricing</a></li>
          <li><a class="text-sm text-gray-400 hover:text-gray-500" href="#">Contact</a></li>
          {% endif %}
        </ul>
            {% if app.user %}
                <a class="lg:inline-block py-2 px-6 bg-blue-500 hover:bg-blue-600 text-sm text-white font-bold rounded-xl transition duration-200" href="/logout" }}">
                    <strong>Déconnexion</strong>
                </a>
            {% endif %}
            {% if not app.user %}
            <a class="button is-primary" href="{{ path('app_login') }}">
                <strong>Se connecter</strong>
            </a>
            {% endif %}
	    </nav>
	    <div class="navbar-menu relative z-50 hidden">
		  <div class="navbar-backdrop fixed inset-0 bg-gray-800 opacity-25"></div>
		    <nav class="fixed top-0 left-0 bottom-0 flex flex-col w-5/6 max-w-sm py-6 px-6 bg-white border-r overflow-y-auto">
			<div class="flex items-center mb-8">
				<button class="navbar-close">
					<svg class="h-6 w-6 text-gray-400 cursor-pointer hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
					</svg>
				</button>
			</div>
			<div>
		<ul>
			<li class="mb-1">
				<a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded" href="/">Accueil</a>
			</li>
          {% if is_granted('ROLE_USER') %}
            <li class="mb-1">
              <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded" href="/posts">Services</a>
            </li>
            <li class="mb-1">
              <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded" href="/articles">Articles</a>
            </li>
            <li class="mb-1">
              <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded" href="/faq">Faq</a>
            </li>
            <li class="mb-1">
              <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded" href="/pages">Pages</a>
            </li>
            <li class="mb-1">
              <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-blue-50 hover:text-blue-600 rounded" href="/category">categories</a>
            </li>
            {% endif %}
				</ul>
			</div>
			<div class="mt-auto">
        <div class="buttons">
            {% if app.user %}
                <a class="button is-black" href="/logout" }}">
                    <strong>Déconnexion</strong>
                </a>
            {% endif %}
            {% if not app.user %}
            <a class="button is-primary" href="{{ path('app_login') }}">
                <strong>Se connecter</strong>
            </a>
            {% endif %}
        </div>
			</div>