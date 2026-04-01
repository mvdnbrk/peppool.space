<x-layout
    title="Inscriptions - peppool.space"
    og_description="Browse the latest Pepecoin inscriptions on peppool.space"
    og_image="pepecoin-inscription.png"
>
    <div class="mb-6 md:mb-8">
        <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
            Inscriptions
        </h1>
    </div>

    <div data-vue="inscriptions-fetcher" data-props='@json(["url" => "/api/inscriptions", "showTitle" => false, "expanded" => true])'></div>
</x-layout>
