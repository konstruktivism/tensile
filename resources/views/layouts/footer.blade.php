<footer class="mb-3 text-center text-lg-start rounded-full">
    <div class="w-full h-8 shadow-inner dark:shadow-neutral-800 relative">
        <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-white dark:from-dark to-transparent w-1/4 h-full z-10"></div>

        <div class="absolute inset-y-0 right-0 bg-gradient-to-l from-white dark:from-dark to-transparent w-1/4 h-full z-10"></div>
    </div>

    <div class="flex flex-col items-center gap-6 md:gap-0 md:flex-row justify-between px-6 pb-3 text-neutral-500">
        {{ config('app.slogan') }}

        <div class="flex items-center gap-2 text-md text-dark dark:text-neutral-500">
            <a class="text-black font-bold rounded-br-lg px-2 bg-yellow-400 lowercase" href="{{ url('/') }}">{{ config('app.name') }}</a>

            <a href="https://konstruktiv.nl" target="_blank" class="flex gap-2">
                built by

                <div class="w-20 fill-current">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 793.157 107.17"><path d="M719.747,16.013H730.58l26.668,66.836,26.168-66.836h10.5l-37.168,91.67ZM181.922,39.181v64.335h-9.667V11.846l56,68.669v-64.5h9.667v91.67ZM463.085,72.515v-56.5h10V71.681c0,14.333,8.834,23.667,21.168,23.667s21.167-9.334,21.167-23.667V16.013h10v56.5c0,19.834-13,32.667-31.167,32.667S463.085,92.349,463.085,72.515Zm176.83,31V25.68H619.081l-2.334-9.667h56.336l-2.334,9.667H649.915v77.836Zm-43.162,0-32.334-44.5v44.5h-10v-87.5h10V57.681l32.667-41.668h11.167l-32.667,42,33,45.5Zm-168,0-27-37.167v37.167h-10v-87.5h17.833c18.834,0,30.668,11.167,30.668,29.335,0,14.834-8.167,24.667-22.168,27.334l22.667,30.834Zm-27-38h8.334c12,0,19.834-8,19.834-20,0-12.167-7.667-19.834-19.334-19.834h-8.834Zm-61.5,38V25.68H319.419l-2.334-9.667h56.336l-2.334,9.667H350.253v77.836Zm-79.666,0,2.334-9.667h18.167c9.667,0,15-4.833,15-11.667,0-6.333-2.833-9.5-7.834-13.334L274.421,58.181c-6.333-4.833-11.167-10.5-11.167-20.667,0-12.834,9.334-21.5,24.835-21.5h18l-2.333,9.667H288.588c-9.667,0-14.834,4.834-14.834,11.834,0,6.167,2.667,9.333,7.667,13.167l13.834,10.667c6.167,4.834,11.167,10.667,11.167,20.834,0,12.667-9.167,21.334-24.667,21.334Zm-187.5,0V93.848h69.835v9.667Zm-30,0-32.334-44.5v44.5h-10v-87.5h10V57.681L43.426,16.013H54.593l-32.667,42,33,45.5Zm648.317-.005V16.01h10v87.5ZM73.423,35.681C73.423,15.846,87.09.512,108.091.512s34.668,15.334,34.668,35.168c0,19.667-14,35.668-34.668,35.668S73.423,55.347,73.423,35.681Zm10.334.167c0,14.167,10,25.5,24.334,25.5s24.334-11.334,24.334-25.5-10-25.667-24.334-25.667S83.757,21.68,83.757,35.847Z" transform="translate(-0.759 -0.512)"/></svg>
                </div>
            </a>
        </div>
    </div>
</footer>
