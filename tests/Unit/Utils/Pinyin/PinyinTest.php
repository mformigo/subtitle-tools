<?php

namespace Tests\Unit;

use App\Utils\Pinyin\Pinyin;
use Tests\TestCase;

class PinyinTest extends TestCase
{
    /** @var $pinyin PinYin */
    protected static $pinyin = null;

    public function setUp()
    {
        parent::setUp();

        // seems to be the fastest and most memory efficient way to load this class
        // reducing all the tests to 1 function doesn't change anything
        if(self::$pinyin === null) {
            self::$pinyin = app('Pinyin');
        }
    }

    /** @test */
    function converting_only_chinese_to_pinyin()
    {
        $this->assertEquals("wǒ yào", self::$pinyin->convert("我要"));
        $this->assertEquals("dìfāng máfan", self::$pinyin->convert("地方 麻烦"));
        $this->assertEquals("wǒ yàoshi huàn le hàomǎ yào tā yǒu shénme yòng", self::$pinyin->convert("我要是換了號碼要它有什么用"));
        $this->assertEquals("péngyou lā", self::$pinyin->convert("朋友拉"));
        $this->assertEquals("tā bèi zhuǎnsòng dào dī jiè hù de yīyuàn", self::$pinyin->convert("他被轉送到低戒護的醫院"));
        $this->assertEquals("bùdéliǎo", self::$pinyin->convert("不得了"));
        $this->assertEquals("le", self::$pinyin->convert("了"));
        $this->assertEquals("shuō", self::$pinyin->convert("說"));
        $this->assertEquals("de", self::$pinyin->convert("的"));
        $this->assertEquals("me", self::$pinyin->convert("么"));
    }

    /** @test */
    function converting_chinese_with_english_to_pinyin()
    {
        $this->assertEquals("zhè shì 17 tiān lǐ dìèrcì", self::$pinyin->convert("这是17天里第二次"));
        $this->assertEquals("14 tiān yòu 16 xiǎoshí", self::$pinyin->convert("14天又16小时"));
        $this->assertEquals("bùjiǔqián xiàwǔ 2 shí 25 fēn", self::$pinyin->convert("不久前下午2时25分"));
        $this->assertEquals("xiōng shì wèi fù 42", self::$pinyin->convert("兄是位富42"));
        $this->assertEquals("wǒmen xūyào tántán guānyú TPS bàogào de shìqing", self::$pinyin->convert("我们需要谈谈关于TPS报告的事情"));
        $this->assertEquals("Milton. fāshēng shénmeshì qíng le?", self::$pinyin->convert("Milton.发生什么事情了？"));
        $this->assertEquals("wǒ xiǎng nǐ yǒu gè hěn hǎo de guānyú Lumbergh", self::$pinyin->convert("我想你有个很好的关于Lumbergh"));
        $this->assertEquals("wǒ shì Bob Slydell. zhè shì wǒ de zhùshǒu", self::$pinyin->convert("我是 Bob Slydell.这是我的助手"));
    }

    /** @test */
    function converting_chinese_that_has_english_in_brackets()
    {
        $this->assertEquals("wǒ (nice) yào", self::$pinyin->convert("我(nice)要"));
        $this->assertEquals("bǐ dàodá (Carry me) zhōngdiǎn gèng měihǎo", self::$pinyin->convert("比到达(Carry me)终点更美好"));
        $this->assertEquals("bǐ dàodá [Carry me] zhōngdiǎn gèng měihǎo", self::$pinyin->convert("比到达[Carry me]终点更美好"));
        $this->assertEquals("bǐ dàodá zhōngdiǎn gèng měihǎo (Carry me)", self::$pinyin->convert("比到达终点更美好(Carry me)"));
        $this->assertEquals("[Carry me] bǐ dàodá zhōngdiǎn gèng měihǎo", self::$pinyin->convert("[Carry me]比到达终点更美好"));
        $this->assertEquals("[good wǒ job]", self::$pinyin->convert("[good 我 job]"));
        $this->assertEquals("(good wǒ job)", self::$pinyin->convert("(good 我 job)"));
    }

    /** @test */
    function converting_when_chinese_is_contained_in_something()
    {
        // Sometimes chinese is surrounded by (weird chinese) quotes
        $this->assertEquals("wǒ shuō: \"hǎo a, yòu shǎo yī jiàn máfan shì\"", self::$pinyin->convert("我说：“好啊，又少一件麻烦事”"));
        $this->assertEquals("\"wǒ W!\"", self::$pinyin->convert("“我W!”"));
        $this->assertEquals("yígè jiào \"yuègòng\" de jiāhuo", self::$pinyin->convert("一个叫“越共”的家伙"));
        $this->assertEquals("wǒ shuō, \"tā shì gè shígànjiā,", self::$pinyin->convert("我说，“他是个实干家，"));
        $this->assertEquals("tā yōngyǒu suǒyǒu wèntí de dáàn\"", self::$pinyin->convert("他拥有所有问题的答案”"));

        $this->assertEquals("\"hǎo a péngyou\"", self::$pinyin->convert("\"好啊 朋友\""));
        $this->assertEquals("'hǎo a péngyou'", self::$pinyin->convert("'好啊 朋友'"));

        // Chinese is rarely in brackets, but it should still work nicely
        $this->assertEquals("chéngwéi kǎmén (jùfēng de zuì)", self::$pinyin->convert("成为卡门(飓风的最)"));
        $this->assertEquals("chéngwéi kǎmén [jùfēng de zuì]", self::$pinyin->convert("成为卡门[飓风的最]"));
        $this->assertEquals("(jùfēng de zuì) chéngwéi kǎmén", self::$pinyin->convert("(飓风的最)成为卡门"));
        $this->assertEquals("[jùfēng de zuì] chéngwéi kǎmén", self::$pinyin->convert("[飓风的最]成为卡门"));
        $this->assertEquals("kǎmén (jùfēng de zuì) chéngwéi", self::$pinyin->convert("卡门(飓风的最)成为"));
        $this->assertEquals("kǎmén [jùfēng de zuì] chéngwéi", self::$pinyin->convert("卡门[飓风的最]成为"));
    }

    /** @test */
    function converting_should_normalize_punctuation()
    {
        $this->assertEquals("wǒ ,.!?:'',.!?:''", self::$pinyin->convert("我 ，。！？：‘’，。！？：‘’"));
        // A space is added before the opening quote (“)
        $this->assertEquals("wǒ said: \"WOW!\"", self::$pinyin->convert("我 said：“WOW!”"));

        $this->assertEquals("[āgānzhèngzhuàn]", self::$pinyin->convert("【阿甘正传】"));
        $this->assertEquals("(nǐ bùshì wǒ de péngyou)", self::$pinyin->convert("（你不是我的朋友）"));
        $this->assertEquals("\"nǐ\"", self::$pinyin->convert("「你」"));
        $this->assertEquals("tā shuō \"bàoqiàn , lìngxiōng guòshì\"", self::$pinyin->convert("他說「抱歉 ， 令兄過世」"));
        $this->assertEquals("\"lìngxiōng shì wèi fùwēng\"", self::$pinyin->convert("「令兄是位富翁」"));
    }

    /** @test */
    function converting_should_handle_punctuation_well()
    {
        $this->assertEquals("děngděng, xiǎo xiōngdì", self::$pinyin->convert("等等，小兄弟"));
        $this->assertEquals("wǒ de shàngdì…", self::$pinyin->convert("我的上帝…"));
        $this->assertEquals("ń…", self::$pinyin->convert("嗯…"));
        $this->assertEquals("nǐ zhǐbuguò shì yìzhī… lièquǎn", self::$pinyin->convert("你只不过是一只…猎犬"));
        $this->assertEquals("māma zài nǎr?", self::$pinyin->convert("妈妈在哪儿？"));
        $this->assertEquals("a! nǐ gāng pǎo guò", self::$pinyin->convert("啊！你刚跑过"));
        $this->assertEquals("dànshì, yīnwèi dìyī:", self::$pinyin->convert("但是，因为第一："));
        $this->assertEquals("yī: bǎohù hǎo nǐ de jiǎo", self::$pinyin->convert("一：保护好你的脚"));
        $this->assertEquals("qī... liù...", self::$pinyin->convert("七...六..."));
        $this->assertEquals("nǐhǎo. wǒ shì fúěr sī", self::$pinyin->convert("你好。我是福尔斯"));
        $this->assertEquals("hēi! qùnǐde!", self::$pinyin->convert("嘿！去你的！"));

        $this->assertEquals("fānyì & jiàoduì & zǒngjiān", self::$pinyin->convert("翻譯&校對&總監"));
        $this->assertEquals("fānyì & jiàoduì &", self::$pinyin->convert("翻譯&校對&"));
        $this->assertEquals("fānyì & & jiàoduì &", self::$pinyin->convert("翻譯&&校對&"));
        $this->assertEquals("& jiàoduì & zǒngjiān", self::$pinyin->convert("&校對&總監"));

        // The ones below are questionable
        $this->assertEquals("bù bā· gān bǔ xiā gōngsī de lǎobǎn?", self::$pinyin->convert("布巴·甘捕虾公司的老板？"));
    }

    /** @test */
    function converting_handles_strange_things()
    {
        // i once saw #'s and *'s being used to mark lyrics
        $this->assertEquals("#shì wèi huīwǔ qízhì#", self::$pinyin->convert("#是为挥舞旗帜#"));
        $this->assertEquals("*shì wèi huīwǔ qízhì*", self::$pinyin->convert("*是为挥舞旗帜*"));
    }

    /** @test */
    function converting_handles_urls_well()
    {
        $this->assertEquals("www.ZiMuZu.tv huānyíng jiāoliú", self::$pinyin->convert("www.ZiMuZu.tv 欢迎交流"));
        $this->assertEquals("jiāoliú www.ZiMuZu.tv huānyíng", self::$pinyin->convert("交流www.ZiMuZu.tv欢迎"));
        $this->assertEquals("www.ZiMuZu.com huānyíng jiāoliú", self::$pinyin->convert("www.ZiMuZu.com 欢迎交流"));

        $this->assertEquals("http://www.ZiMuZu.tv huānyíng jiāoliú", self::$pinyin->convert("http://www.ZiMuZu.tv 欢迎交流"));
        $this->assertEquals("jiāoliú http://www.ZiMuZu.tv huānyíng", self::$pinyin->convert("交流http://www.ZiMuZu.tv欢迎"));
        $this->assertEquals("http://www.ZiMuZu.com huānyíng jiāoliú", self::$pinyin->convert("http://www.ZiMuZu.com 欢迎交流"));

        $this->assertEquals("https://www.ZiMuZu.tv huānyíng jiāoliú", self::$pinyin->convert("https://www.ZiMuZu.tv 欢迎交流"));
        $this->assertEquals("jiāoliú https://www.ZiMuZu.tv huānyíng", self::$pinyin->convert("交流https://www.ZiMuZu.tv欢迎"));
        $this->assertEquals("https://www.ZiMuZu.com huānyíng jiāoliú", self::$pinyin->convert("https://www.ZiMuZu.com 欢迎交流"));
    }

    /** @test */
    function converting_handles_spaces_well()
    {
        $this->assertEquals("- nà hǎo - hǎo", self::$pinyin->convert("- 那好 - 好"));
        $this->assertEquals("- nà hǎo - hǎo", self::$pinyin->convert("- 那好  - 好"));
        $this->assertEquals("- nà hǎo - hǎo", self::$pinyin->convert("- 那好   - 好"));
        $this->assertEquals("- nà hǎo    - hǎo", self::$pinyin->convert("- 那好    - 好"));
        $this->assertEquals("- nà hǎo    - hǎo", self::$pinyin->convert("- 那好     - 好"));
        $this->assertEquals("- nà hǎo    - hǎo", self::$pinyin->convert("- 那好      - 好"));
        $this->assertEquals("- nà hǎo    - hǎo", self::$pinyin->convert("- 那好                               - 好"));
        $this->assertEquals("- kàn guò    - nǐ zhīdào zěnme kàn dìtú ma", self::$pinyin->convert("- 看过      - 你知道怎么看地图吗"));
    }

    /** @test */
    function it_returns_the_input_string_if_no_chinese_to_convert()
    {
        $input = [
            " ，。！？：‘’，。！？：‘’",
            "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVW 1234567890-=_,.()[]{}~!@#$%^&*+",
            "This man is out of ideas.",
            "What is Holland?",
            "What(is)Holland?",
        ];

        foreach($input as $string) {
            $this->assertSame($string, self::$pinyin->convert($string));
        }

    }
}
