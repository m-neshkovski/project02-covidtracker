$(document).ready(function () {

    var filter = 'TotalConfirmed';
    var title = '/Confirmed/Cumulative';
    var dataForChartData = [];
    var dataForChartDataCountry = [];
    var is_Active = false;
    var selectedCountry = 'world';
    var selectedPeriod = 'false';
    // data for charts
    var chartData = {
        Country: [],
        TotalConfirmed: [],
        TotalDeaths: [],
        TotalRecovered: [],
        NewConfirmed: [],
        NewDeaths: [],
        NewRecovered: [],
    };
    var chartDataCountry = {
        Country: [],
        TotalConfirmed: [],
        tcma: [],
        TotalDeaths: [],
        tdma: [],
        TotalRecovered: [],
        trma: [],
        NewConfirmed: [],
        ncma: [],
        NewDeaths: [],
        ndma: [],
        NewRecovered: [],
        nrma: [],
        Active: [],
        ama: [],
    };
    // build empty charts
    var options_chart_world = {
        series: [],
        chart: {
            height: 800,
            type: 'bar',
            zoom: {
                type: 'x',
                enabled: true,
                autoScaleYaxis: true
            },
            toolbar: {
                autoSelected: 'zoom'
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '90%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 1,
            colors: ['transparent']
        },
        noData: {
            text: 'Loading...'
        },
        xaxis: {
            tickPlacement: 'on',
        },
        yaxis: {
            title: {
                text: 'Population affected'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " cases"
                }
            }
        }
    }
    var chart_world = new ApexCharts(document.querySelector('#chart-world'), options_chart_world);
    chart_world.render();
    var options_chart_country = {
        series: [],
        chart: {
            type: 'line',
            stacked: false,
            height: 800,
            zoom: {
                type: 'x',
                enabled: true,
                autoScaleYaxis: true
            },
            toolbar: {
                autoSelected: 'zoom'
            }
        },
        stroke: {
            width: [2, 2],
            curve: 'smooth'
          },
        dataLabels: {
            enabled: false
        },
        markers: {
            size: 0,
        },
        noData: {
            text: 'Loading...'
        },
        fill: {
            type: 'solid',
            gradient: {
              shadeIntensity: 1,
              inverseColors: false,
              opacityFrom: 0.5,
              opacityTo: 0,
              stops: [0, 90, 100]
            },
          },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return (val / 1).toFixed(0);
                },
            },
            title: {
                text: 'Population affected'
            },
            tickPlacement: 'on',
        },
        xaxis: {
            type: 'Date',
        },
        tooltip: {
            shared: true,
            y: {
                formatter: function (val) {
                    return (val / 1).toFixed(0)
                }
            }
        }
    };
    var chart_country = new ApexCharts(document.querySelector('#chart-country'), options_chart_country);
    chart_country.render();

    function printTableIfWorld(response) {
        $('#all-countries').html('');
        $('#data-for-span-country').text(response.Country);
        let period = 'All time';
        if (response.Period != 'false') {
            if (response.Period == 1) {
                period = 'Last day';
            } else {
                period = `Last ${response.Period} days`;
            }
        }
        $('#data-for-span-period').text(period)
        $('#all-countries').html(`
        <thead class="text-center thead-light thead-light-bg">
                        <tr>
                            <th scope="col" class="text-center" rowspan="2">#</th>
                            <th scope="col" class="text-center" rowspan="2">Country</th>
                            <th class="text-center" colspan="3">Total in period</th>
                            <th class="text-center" colspan="3">Total in Last Day</th>
                            <th scope="col" class="text-center" rowspan="2">Period active</th>
                        </tr>
                        <tr>

                            
                            <th scope="col" class="text-center">Confirmed</th>
                            <th scope="col" class="text-center">Deaths</th>
                            <th scope="col" class="text-center">Recovered</th>
                            <th scope="col" class="text-center">Confirmed</th>
                            <th scope="col" class="text-center">Deaths</th>
                            <th scope="col" class="text-center">Recovered</th>
                            
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr id="world">
                            <td scope="row">0</td>
                            <td>World</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </tr>
                    </tbody>
        `);
        $('#total-confirmed').text(numberWithDots(response.Global.TotalConfirmed))
        $('#new-confirmed').text(numberWithDots(response.Global.NewConfirmed))
        $('#total-deaths').text(numberWithDots(response.Global.TotalDeaths))
        $('#new-deaths').text(numberWithDots(response.Global.NewDeaths))
        $('#total-recovered').text(numberWithDots(response.Global.TotalRecovered))
        $('#new-recovered').text(numberWithDots(response.Global.NewRecovered))
        let date = new Date(response.Date)
        $('#last-updated span').text(date.toUTCString());
        $('#world').children().eq(0).text('0')
        $('#world').children().eq(1).text('World')
        $('#world').children().eq(2).text(numberWithDots(response.Global.TotalConfirmed))
        $('#world').children().eq(3).text(numberWithDots(response.Global.TotalDeaths))
        $('#world').children().eq(4).text(numberWithDots(response.Global.TotalRecovered))
        $('#world').children().eq(5).text(numberWithDots(response.Global.NewConfirmed))
        $('#world').children().eq(6).text(numberWithDots(response.Global.NewDeaths))
        $('#world').children().eq(7).text(numberWithDots(response.Global.NewRecovered))
        $('#world').children().eq(8).text(numberWithDots(response.Global.TotalConfirmed - response.Global.TotalDeaths - response.Global.TotalRecovered))
        let tableIndex = 1
        response.Countries.forEach(country => {
            $('#all-countries tbody').append(`
                <tr class="text-center">
                    <td scope="row" class="text-center">${tableIndex}</td>
                    <td class="text-center">${country.Country}</td>
                    <td class="text-center">${numberWithDots(country.TotalConfirmed)}</td>
                    <td class="text-center">${numberWithDots(country.TotalDeaths)}</td>
                    <td class="text-center">${numberWithDots(country.TotalRecovered)}</td>
                    <td class="text-center">${numberWithDots(country.NewConfirmed)}</td>
                    <td class="text-center">${numberWithDots(country.NewDeaths)}</td>
                    <td class="text-center">${numberWithDots(country.NewRecovered)}</td>
                    <td class="text-center">${numberWithDots(country.TotalConfirmed - country.TotalDeaths - country.TotalRecovered)}</td>
                </tr>
            `)
            tableIndex++
        })
    }

    function printTableIfCountry(response) {
        $('#all-countries').html('');
        $('#data-for-span-country').text(response.Country);

        let period = 'All time';
        if (response.Period != 'false') {
            if (response.Period == 1) {
                period = 'Last day';
            } else {
                period = `Last ${response.Period} days`;
            }
        }
        $('#data-for-span-period').text(period)
        $('#all-countries').html(`
        <thead class="text-center thead-light thead-light-bg">
                        <tr>
                            <th scope="col" class="text-center" rowspan="2">#</th>
                            <th scope="col" class="text-center" rowspan="2">Country total / Date cumulative</th>
                            <th class="text-center" colspan="3">Total in period / Cumulative by date</th>
                            <th class="text-center" colspan="3">Total in Last Day / Total in day</th>
                            <th scope="col" class="text-center" rowspan="2">Period active</th>
                        </tr>
                        <tr>

                            
                            <th scope="col" class="text-center">Confirmed</th>
                            <th scope="col" class="text-center">Deaths</th>
                            <th scope="col" class="text-center">Recovered</th>
                            <th scope="col" class="text-center">Confirmed</th>
                            <th scope="col" class="text-center">Deaths</th>
                            <th scope="col" class="text-center">Recovered</th>
                            
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr id="world">
                            <td scope="row">0</td>
                            <td>World</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </tr>
                    </tbody>
        `);
        $('#total-confirmed').text(numberWithDots(response.Global.TotalConfirmed))
        $('#new-confirmed').text(numberWithDots(response.Global.NewConfirmed))
        $('#total-deaths').text(numberWithDots(response.Global.TotalDeaths))
        $('#new-deaths').text(numberWithDots(response.Global.NewDeaths))
        $('#total-recovered').text(numberWithDots(response.Global.TotalRecovered))
        $('#new-recovered').text(numberWithDots(response.Global.NewRecovered))
        let date = new Date(response.Date)
        $('#last-updated span').text(date.toUTCString());
        $('#world').children().eq(0).text('0')
        $('#world').children().eq(1).text('Total in period')
        $('#world').children().eq(2).text(numberWithDots(response.Global.TotalConfirmed))
        $('#world').children().eq(3).text(numberWithDots(response.Global.TotalDeaths))
        $('#world').children().eq(4).text(numberWithDots(response.Global.TotalRecovered))
        $('#world').children().eq(5).text(numberWithDots(response.Global.NewConfirmed))
        $('#world').children().eq(6).text(numberWithDots(response.Global.NewDeaths))
        $('#world').children().eq(7).text(numberWithDots(response.Global.NewRecovered))
        $('#world').children().eq(8).text(numberWithDots(response.Global.TotalConfirmed - response.Global.TotalDeaths - response.Global.TotalRecovered))
        let tableIndex = 1
        response.Countries.forEach(country => {
            $('#all-countries tbody').append(`
                <tr class="text-center">
                    <td scope="row" class="text-center">${tableIndex}</td>
                    <td class="text-center">${country.Country}</td>
                    <td class="text-center">${numberWithDots(country.TotalConfirmed)}</td>
                    <td class="text-center">${numberWithDots(country.TotalDeaths)}</td>
                    <td class="text-center">${numberWithDots(country.TotalRecovered)}</td>
                    <td class="text-center">${numberWithDots(country.NewConfirmed)}</td>
                    <td class="text-center">${numberWithDots(country.NewDeaths)}</td>
                    <td class="text-center">${numberWithDots(country.NewRecovered)}</td>
                    <td class="text-center">${numberWithDots(country.TotalConfirmed - country.TotalDeaths - country.TotalRecovered)}</td>
                </tr>
            `)
            tableIndex++
        })
    }

    function numberWithDots(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function buildChartData(array) {
        chartData = {
            Country: [],
            TotalConfirmed: [],
            TotalDeaths: [],
            TotalRecovered: [],
            NewConfirmed: [],
            NewDeaths: [],
            NewRecovered: [],
        };

        array.forEach(country => {
            chartData.Country.push(country.Country)
            chartData.TotalConfirmed.push({ "x": country.Country, "y": parseInt(country.TotalConfirmed) })
            chartData.TotalDeaths.push({ "x": country.Country, "y": parseInt(country.TotalDeaths) })
            chartData.TotalRecovered.push({ "x": country.Country, "y": parseInt(country.TotalRecovered) })
            chartData.NewConfirmed.push({ "x": country.Country, "y": parseInt(country.NewConfirmed) })
            chartData.NewDeaths.push({ "x": country.Country, "y": parseInt(country.NewDeaths) })
            chartData.NewRecovered.push({ "x": country.Country, "y": parseInt(country.NewRecovered) })
        })
        return chartData;
    }

    function calcXDayAVG(array, len, is_result_float) {
        // array[x] {
        //     x: ime na dezava,
        //     y:brojka
        // }
        tempArr = [];
        for(let i = 0; i < array.length; i++) {
            sum = 0;
            for(let j = 0; j < len; j++) {
                if ((i - j) < 0) {
                    sum += 0;
                } else {
                    sum += array[i-j].y;
                }
            }
            if(is_result_float) {
                tempArr.push({
                    x: array[i].x,
                    y: sum/len,
                })
            } else {
                tempArr.push({
                    x: array[i].x,
                    y: Math.floor(sum/len),
                })
            }
        }
        return tempArr;
    }

    function numberOfLastResultsFromArray(array, period) {
        if (period == 'false') {
            return array
        } else {
            return array.slice(Math.max(array.length - parseInt(period), 0));
        }
    }

    function buildChartDataCountry(array) {
        chartDataCountry.Country = [];
        chartDataCountry.TotalConfirmed = [];
        chartDataCountry.TotalDeaths = [];
        chartDataCountry.TotalRecovered = [];
        chartDataCountry.NewConfirmed = [];
        chartDataCountry.NewDeaths = [];
        chartDataCountry.NewRecovered = [];
        chartDataCountry.Active = [];

        array = array.reverse();

        array.forEach(country => {
            chartDataCountry.Country.push(country.Country)
            chartDataCountry.TotalConfirmed.push({ "x": country.Country, "y": parseInt(country.TotalConfirmed) })
            chartDataCountry.TotalDeaths.push({ "x": country.Country, "y": parseInt(country.TotalDeaths) })
            chartDataCountry.TotalRecovered.push({ "x": country.Country, "y": parseInt(country.TotalRecovered) })
            chartDataCountry.NewConfirmed.push({ "x": country.Country, "y": parseInt(country.NewConfirmed) })
            chartDataCountry.NewDeaths.push({ "x": country.Country, "y": parseInt(country.NewDeaths) })
            chartDataCountry.NewRecovered.push({ "x": country.Country, "y": parseInt(country.NewRecovered) })
            chartDataCountry.Active.push({ "x": country.Country, "y": parseInt(country.TotalConfirmed) - parseInt(country.TotalDeaths) - parseInt(country.TotalRecovered) })
        })
        return chartDataCountry;
    }

    function buildChartDataCountryMovingAverage(array, steps, is_res_float) {

        chartDataCountry.tcma = calcXDayAVG(array.TotalConfirmed, steps, is_res_float);
        chartDataCountry.tdma = calcXDayAVG(array.TotalDeaths, steps, is_res_float);
        chartDataCountry.trma = calcXDayAVG(array.TotalRecovered, steps, is_res_float);
        chartDataCountry.ncma = calcXDayAVG(array.NewConfirmed, steps, is_res_float);
        chartDataCountry.ndma = calcXDayAVG(array.NewDeaths, steps, is_res_float);
        chartDataCountry.nrma = calcXDayAVG(array.NewRecovered, steps, is_res_float);
        chartDataCountry.ama = calcXDayAVG(array.Active, steps, is_res_float);

    } 


    function refreshCharts() {
        switch (filter) {
            case 'TotalConfirmed':
                dataForChartData = chartData.TotalConfirmed;
                dataForChartDataCountry = numberOfLastResultsFromArray(chartDataCountry.TotalConfirmed, selectedPeriod);
                dataForChartDataCountryMA = numberOfLastResultsFromArray(chartDataCountry.tcma, selectedPeriod);
                break;
            case 'TotalDeaths':
                dataForChartData = chartData.TotalDeaths;
                dataForChartDataCountry = numberOfLastResultsFromArray(chartDataCountry.TotalDeaths, selectedPeriod);
                dataForChartDataCountryMA = numberOfLastResultsFromArray(chartDataCountry.tdma, selectedPeriod);
                break;
            case 'TotalRecovered':
                dataForChartData = chartData.TotalRecovered;
                dataForChartDataCountry = numberOfLastResultsFromArray(chartDataCountry.TotalRecovered, selectedPeriod);
                dataForChartDataCountryMA = numberOfLastResultsFromArray(chartDataCountry.trma, selectedPeriod);
                break;
            case 'NewConfirmed':
                dataForChartData = chartData.NewConfirmed;
                dataForChartDataCountry = numberOfLastResultsFromArray(chartDataCountry.NewConfirmed, selectedPeriod);
                dataForChartDataCountryMA = numberOfLastResultsFromArray(chartDataCountry.ncma, selectedPeriod);
                break;
            case 'NewDeaths':
                dataForChartData = chartData.NewDeaths;
                dataForChartDataCountry = numberOfLastResultsFromArray(chartDataCountry.NewDeaths, selectedPeriod);
                dataForChartDataCountryMA = numberOfLastResultsFromArray(chartDataCountry.ndma, selectedPeriod);
                break;
            case 'NewRecovered':
                dataForChartData = chartData.NewRecovered;
                dataForChartDataCountry = numberOfLastResultsFromArray(chartDataCountry.NewRecovered, selectedPeriod);
                dataForChartDataCountryMA = numberOfLastResultsFromArray(chartDataCountry.nrma, selectedPeriod);
                break;
            case 'Active':
                dataForChartDataCountry = numberOfLastResultsFromArray(chartDataCountry.Active, selectedPeriod);
                dataForChartDataCountryMA = numberOfLastResultsFromArray(chartDataCountry.ama, selectedPeriod);
                is_Active = true;
                break;
            default:
                break;
        }

        chart_world.updateSeries([{
            name: filter,
            data: dataForChartData,
        }])

        chart_country.updateSeries([{
            name: filter,
            type: 'line',
            data: dataForChartDataCountry,
        },
        {
            name: 'MovingAverage',
            type: 'line',
            data: dataForChartDataCountryMA,
        }])
    }


    $('#charts-row').slideUp();
    $('#chart-country-wrapper').slideUp();

    fetch('./cms/php/api/periodWorldSummary.php?country=world&period=false')
        .then(response => {
            return response.json()
        })
        .then(resp => {
            printTableIfWorld(resp);
            resp.Active_countries.forEach(country => {
                $('#countries-select').append(`
                <option value="${country.ISO2}">${country.Country}</option>
            `)
            })
            buildChartData(resp.Countries);
            chart_world.updateSeries([{
                name: 'TotalConfirmed',
                data: chartData.TotalConfirmed,
            }])
            let per = 'All time';
            if (resp.Period != 'false') {
                if (resp.Period == 1) {
                    per = 'Last day';
                } else {
                    per = `Last ${resp.Period} days`;
                }
            }
            $('#chart-name').html(`${resp.Country}: <br>${per}`);
            $('#chart-world-wrapper').slideDown();
            $('#chart-country-wrapper').slideUp();
        })

    $('#filter-form').on('change', function (e) {
        e.preventDefault();
        let country = $('#countries-select').val()
        let period = $('#period-select').val();
        if (country == 'world') {
            if (!(selectedCountry == country && selectedPeriod == period)) {
                fetch(`./cms/php/api/periodWorldSummary.php?country=${country}&period=${period}`)
                    .then(response => {
                        return response.json()
                    })
                    .then(data => {
                        printTableIfWorld(data);
                        buildChartData(data.Countries);
                        if (is_Active) {
                            filter = 'TotalConfirmed';
                            title = '/Confirmed/Cumulative';
                            let btns = $('.filter-btns');
                            for (let i = 0; i < btns.length; i++) {
                                $(btns[i]).parents().eq(1).removeClass('active')
                                $(btns[i]).removeClass('active')
                            }
                            $('#total-confirmed-btn').addClass('active');
                            $('#total-confirmed-btn').parents().eq(1).addClass('active');
                            $('#chart-data-selected').text(title);
                            is_Active = false;
                        }
                        refreshCharts();
                        let per = 'All time';
                        if (data.Period != 'false') {
                            if (data.Period == 1) {
                                per = 'Last day';
                            } else {
                                per = `Last ${data.Period} days`;
                            }
                        }
                        $('#chart-name').html(`${data.Country}: <br>${per}`);
                        $('#chart-world-wrapper').slideDown();
                        $('#chart-country-wrapper').slideUp();
                        $('#active-btn').addClass('disabled');
                    })
                selectedCountry = country;
                selectedPeriod = period;
            }
        } else {
            if (!(selectedCountry == country && selectedPeriod == period)) {
                if(selectedPeriod != country) {
                    fetch(`./cms/php/api/periodWorldSummary.php?country=${country}&period=false`)
                    .then(response => {
                        return response.json()
                    })
                    .then(data => {
                        return buildChartDataCountry(data.Countries)
                    })
                    .then(ma => {
                        buildChartDataCountryMovingAverage(ma, 7, false);
                    })
                }
                fetch(`./cms/php/api/periodWorldSummary.php?country=${country}&period=${period}`)
                    .then(response => {
                        return response.json()
                    })
                    .then(data => {
                        printTableIfCountry(data);
                        buildChartDataCountry(data.Countries)
                        refreshCharts();
                        let per = 'All time';
                        if (data.Period != 'false') {
                            if (data.Period == 1) {
                                per = 'Last day';
                            } else {
                                per = `Last ${data.Period} days`;
                            }
                        }
                        $('#chart-name').html(`${data.Country}:<br>${per}`);
                        $('#chart-world-wrapper').slideUp();
                        $('#chart-country-wrapper').slideDown();
                        $('#active-btn').removeClass('disabled');
                    })
                selectedCountry = country;
                selectedPeriod = period;
            }
        }
    });

    $('#charts-check').on('change', function (e) {
        e.preventDefault();

        $('#card-wrapper').slideToggle();
        $('#charts-row').slideToggle();
        $('#table-row').slideToggle();
    })

    $('.filter-btns').on('click', function (e) {
        e.preventDefault();
        let clicked = $(e.target)
        filter = clicked.data('filter');
        title = clicked.data('title');
        let btns = $('.filter-btns');
        for (let i = 0; i < btns.length; i++) {
            $(btns[i]).parents().eq(1).removeClass('active')
            $(btns[i]).removeClass('active')
        }
        clicked.addClass('active');
        clicked.parents().eq(1).addClass('active');
        $('#chart-data-selected').text(title);
        refreshCharts();
    })

})